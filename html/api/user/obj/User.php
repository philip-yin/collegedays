<?
require_once('/var/www/html/api/obj/CDObject.php');

class User extends CDObject
{
	//Class constructor
	function User($identifier = NULL, $PDOconn = NULL)
	{
		//Call super constructor
		parent::CDObject('user', $PDOconn);

		//Try to identify the user
		if($identifier == NULL)
			return;
			
			//First try to search by ID
			$sql = "SELECT objectID FROM user WHERE objectID=:objectID";
			$stmtA = $this->PDOconn->prepare($sql);
			$paramsA[':objectID'] = $identifier;
			$stmtA->execute($paramsA);
			
			//Any users by id?
			if($stmtA->rowCount() > 0)
			{
				$row = $stmtA->fetch();
				$this->ID = $row['objectID'];
				$this->exists = true;
				$this->refresh();
				return; //Done
			}

			//User not found by id, try email
			$sql = "SELECT objectID FROM user WHERE email=:email";
			$stmtB = $this->PDOconn->prepare($sql);
			$paramsB[':email'] = $identifier;
			$stmtB->execute($paramsB);
			
			//Any users by email?
			if($stmtB->rowCount() > 0)
			{
				$row = $stmtB->fetch();
				$this->ID = $row['objectID'];
				$this->exists = true;
				$this->refresh();
				return;
			}
			
			//Try by row
			$sql = "SELECT objectID FROM user WHERE row=:row";
			$stmtC = $this->PDOconn->prepare($sql);
			$paramsC[':row'] = $identifier;
			$stmtC->execute($paramsC);
			
			if($stmtC->rowCount() > 0)
			{
				$row = $stmtC->fetch();
				$this->ID = $row['objectID'];
				$this->exists = true;
				$this->refresh();
				return;
			}
	}

	//Creates a new user, returns true if successful
	public function create($fName, $lName, $email, $password)
	{	
		$response[0]['status'] = 0;
		$response[1]['reason'] = "";
	
		if($this->exists)
		{
			$response[1]['reason'] = 'The user already exists';
			return $response;
		}
		
		//See if email is valid
		if(!( filter_var($email, FILTER_VALIDATE_EMAIL)))
		{
		    $response[1]['reason'] = 'The email was invalid';
			return $response;
		}
		
		//See if the password is valid (4 characters)
		if(strlen($password) < 4)
		{
			$response[1]['reason'] = 'Password is too short';
			return $response;
		}
		
		//Check for an edu email
		$emailLength = strlen($email);
		$emailDomain = '';
		$emailSchool = '';
		$grabbing = 'domain';
		
		$i = $emailLength;
		while($i > 0)
		{
			if($email[$i] == '.')
				$grabbing = 'school';
			else if($email[$i] == '@')
				break;
			else
			{
				if($grabbing == 'domain')
					$emailDomain .= $email[$i];
				else
					$emailSchool .= $email[$i];
			}
			
			$i--;
		}
		
		if($emailDomain != "ude") //edu backwards
		{
			$response[1]['reason'] = 'CollegeDays is only for students right now';
			return $response;
		}
		
		$emailSchool = strtoupper(strrev($emailSchool));
		
		if($emailSchool != "UCSD")
		{
			$response[1]['reason'] = 'CollegeDays is only for UCSD students right now';
			return $response;
		}
		
		//Valid data
		//Create a password salt and hash
		$passSalt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$passHash = hash('sha256', $password.$passSalt);
				
		//Create user
		$uniqueID = $this->generateUniqueID();
		$sql = "INSERT INTO user (objectID, email, school, fName, lName, passHash, passSalt) VALUES
								 (:objectID, :email, :school, :fName, :lName, :passHash, :passSalt)";
		$stmtI = $this->PDOconn->prepare($sql); 
		$paramsI[':objectID'] = $uniqueID;
		$paramsI[':email'] = $email;
		$paramsI[':fName'] = $fName;
		$paramsI[':lName'] = $lName;
		$paramsI[':passHash'] = $passHash;
		$paramsI[':passSalt'] = $passSalt;
		$paramsI[':school'] = $emailSchool;
		$successI = $stmtI->execute($paramsI);
		
		if($successI)
		{
			$this->ID = $uniqueID;
			$this->exists = true;
			$this->refresh();
			
			$sql = "UPDATE user SET lastRow=:lastRow WHERE objectID=:objectID";
			$stmtN = $this->PDOconn->prepare($sql);
			$paramsN[':objectID'] = $this->ID;
			$paramsN[':lastRow'] = $this->row['row'];
			$stmtN->execute($paramsN);
		}
		
		$response[0]['status'] = 1;
		return $response;
	}
	
	//Returns the matchID of the current user's match
	public function getCurrentMatchID()
	{
		/*
							Get the match here
							  |
							  V
					  min           max	
		|             |             |             |
		12am yday     12am tday     12am tmrw      
		
		*/
		
		//Get bounding timestamps
		$minTime = strtotime("midnight", time());
		$maxTime = strtotime("tomorrow", $minTime) - 1;
				
		//Search for a match that involves the user and is within the max and min timestamps (today)
		$sql = "SELECT objectID FROM mach WHERE (userID_a=:userID_1 OR userID_b=:userID_2) AND 
				(creationTime >= :minTime AND creationTime <= :maxTime) LIMIT 1";
				
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':minTime'] = $minTime;
		$paramsA[':maxTime'] = $maxTime;
		$paramsA[':userID_1'] = $this->ID;
		$paramsA[':userID_2'] = $this->ID;
		$stmtA->execute($paramsA);
		
		//If there was a result
		if($stmtA->rowCount() > 0)
		{
			$row = $stmtA->fetch();
			return $row['objectID'];
		}
		
		//Return blank id
		return '';
	}
	
	public function refreshMatched()
	{
		if($this->getCurrentMatchID() == '')
			$this->setMatched(0);
		else
			$this->setMatched(1);
	}
	
	public function setMatched($matched)
	{
		$sql = "UPDATE user SET currentlyMatched=:matched WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':matched'] = $matched;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
	}
	
	public function setLastRow($lastChecked)
	{
		$sql = "UPDATE user SET lastRow=:lastRow WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':lastRow'] = $lastChecked;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
	}
	public function change_email($password, $original_email, $new_email, $confirm_new_email)
	{
		$response[0]['status'] = 0;
		$response[1]['reason'] = "";
		
		//Does the user use this password?
		if(!$this->usesPassword($password))
		{
			$response[1]['reason'] = "Incorrect password."; 
			return $response;
		}
		if($this->row['email']!= $original_email )
		{
			$response[1]['reason'] = "Incorrect original email"; 
			return $response;
		}
		if($confirm_new_email != $new_email)
		{
			$response[1]['reason'] = "The email you want to change does not match."; 
			return $response;
		}
		$sql = "UPDATE user SET email=:confirm_new_email WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':confirm_new_email'] = $confirm_new_email;
		$paramsA[':objectID'] = $this->ID;

		$stmtA->execute($paramsA);
		
		$response[0]['status'] = 1;
		return $response;


	}

	public function edit_name($password, $fName, $lName)
	{

		$response[0]['status'] = 0;
		$response[1]['reason'] = "";
		
		//Does the user use this password?
		if(!$this->usesPassword($password))
		{
			$response[1]['reason'] = "Incorrect password."; 
			return $response;
		}

		$sql = "UPDATE user SET fname=:fName, lName=:lName WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':fName'] = $fName;
		$paramsA[':lName'] = $lName;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
		
		$response[0]['status'] = 1;
		return $response;
	}

	public function disableAccount($password)
	{
		$response[0]['status'] = 0;
		$response[1]['reason'] = "";
		
		//Does the user use this password?
		if(!$this->usesPassword($password))
		{
			$response[1]['reason'] = "Incorrect password."; 
			return $response;
		}
		
		$sql = "UPDATE user SET disabled='1' WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
		
		$response[0]['status'] = 1;
		return $response;
	}
	
	//Returns true or false if the user uses the password
	public function usesPassword($password = '')
	{
		if($password == '' || !$this->exists)
			return false;

		//Refresh the user
		$this->refresh();

		//Get the password salt for the user
		$passSalt = $this->row['passSalt'];

		//Create a test password hash
		$passHash = hash('sha256', $password.$passSalt);

		//Does it equal the user's password hash?
		if(!($passHash == $this->row['passHash']))
			return false; //Incorrect password

		//The password resolved to the correct 
		return true;
	}
	
	//Convesations
	public function getConversationWith($userID, $makeNew = false)
	{
		if(!$this->exists) return false;
		require_once('/var/www/html/api/conversation/obj/Conversation.php');
		$Conversation = new Conversation(NULL, $this->PDOconn);
		$Conversation->findConversation($userID, $this->ID);
		
		if(!$Conversation->exists)
		{
			//No conversation, can we make one?
			$canConverse = true;
			
			//Is the requested chat the current match for $this?
			require_once('/var/www/html/api/match/obj/Match.php');
			$Match = new Match($this->getCurrentMatchID());
			if($Match->getNotUserID($this->ID) != $userID)
				$canConverse = false;
			
			if($canConverse && $makeNew)
			{
				//Create the conversation
				$Conversation->create($this->ID, $userID);
			}
		}
		
		return $Conversation;
	}
}

?>
