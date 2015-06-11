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
	
	public function isVerified()
	{
		$email = $this->row['email'];
		$sql = "SELECT verified FROM verify WHERE email=:email AND userID=:userID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':email'] = $email;
		$paramsA[':userID'] = $this->ID;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() == 0) return 0;
		
		$row = $stmtA->fetch();
		return $row['verified'];
	}
	
	public function verify($key)
	{
		if(!$this->exists) return false;
		if($this->isVerified()) return false;
		
		$sql = "UPDATE verify SET verified='1' WHERE email=:email AND userID=:userID AND verification_key=:key";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':email'] = $this->row['email'];
		$paramsA[':userID'] = $this->ID;
		$paramsA[':key'] = $key;
		return $stmtA->execute($paramsA);
	}
	
	public function change_description($description)
	{
		$response[0]['status'] = 0;
		//Check description size
		$minLength = CDConsts::getConst('MIN_DESCRIPTION', 'intValue', $this->PDOconn);
		$maxLength = CDConsts::getConst('MAX_DESCRIPTION', 'intValue', $this->PDOconn);
		$description_length = strlen($description);
		
		if($description_length < $minLength || $desciption_length > $maxLength)
		{
			$response[1]['reason'] = "The description size is incorrect.";
			return $response;
		}
		
		$description = strip_tags($description);
		
		$sql = "UPDATE user SET description=:description WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':description'] = $description;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
		
		$response[0]['status'] = 1;
		return $response;
	}
	
	public function getImageURL()
	{
		require_once('/var/www/html/api/image/obj/Image.php');
		$Image = new Image($this->row['imageID']);
		
		return $Image->getURL();
	}
	
	public function setMatching($matching)
	{
		if(!$this->exists) return false;
		
		if(!$this->isVerified()) return false;
		
		$sql = "UPDATE user SET matching=:matching WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':matching'] = $matching;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
		$this->refresh();
		
		if($matching == 0)
		{
			$this->setLooking(' ');
		}
		
		return true;
	}
	
	public function setLooking($string)
	{
		$valid = false;
		if($string == ' ' ||
		   $string == 'm' ||
		   $string == 'f' ||
		   $string == 'b')
		   $valid = true;
		   
		if(!$valid) return false;
		 
		$sql = "UPDATE user SET looking=:looking WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':looking'] = $string;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
		$this->refresh();
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
		
		//Email verification link to user
		$this->verifyEmail();
		
		$response[0]['status'] = 1;
		return $response;
	}
	
	public function verifyEmail()
	{
		$userEmail = $this->row['email'];

		//Send verification link
		$key = $this->getVerificationKey($userEmail);
		
		//Make link
		$verificationLink = "http://gocollegedays.com/?key=".$key."&user=".$this->ID;
		
		//The source
		$sourceAddress = "CollegeDays <hello@gocollegedays.com>";
		
		//The subject header
		$subjectHeader = "Hello, ".$this->row['fName']."!";
		
		//Body text
		$bodyText = 'Follow this link to verfy your '.$this->row['school'].' email: <a href="'.$verificationLink.'">Click to verify your email</a>';
		
		//Body HTML
		$titleText = 'Please verify your '.$this->row['school'].' email!';
		$bodyHTML = 
		'<p>
		<a href="'.$verificationLink.'">Follow this link to verify your email</a>
		</p>
		';
		
		//Send the email
		require_once('/var/www/html/src/php/mail/mail.php');
		sendEmail($sourceAddress, $userEmail, $subjectHeader, $titleText, $bodyText, $bodyHTML, $this->PDOconn);
	}
	
	public function getVerificationKey($email)
	{
		$sql = "SELECT verification_key FROM verify WHERE email=:email";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':email'] = $email;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() > 0)
		{
			$row = $stmtA->fetch();
			$key = $row['verification_key'];
		}	
		else
		{
			do
			{
				$key = CDTools::randString(32);
				$sql = "SELECT row FROM verify WHERE verification_key=:verification_key";
				$stmtB = $this->PDOconn->prepare($sql);
				$paramsB[':verification_key'] = $key;
				$stmtB->execute($paramsB);
			}
			while($stmtB->rowCount() > 0);
			
			$sql = "INSERT INTO verify (verification_key, email, userID) VALUES (:verification_key, :email, :userID)";
			$stmtC = $this->PDOconn->prepare($sql);
			$paramsC[':verification_key'] = $key;
			$paramsC[':email'] = $email;
			$paramsC[':userID'] = $this->ID;
			$stmtC->execute($paramsC);
		}
		
		return $key;
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
		$table = "mach";
		if($this->istest) $table .= "_test";
		$sql = "SELECT objectID FROM $table WHERE (userID_a=:userID_1 OR userID_b=:userID_2) AND 
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
		$table = $this->getTableName();
		
		$sql = "UPDATE $table SET currentlyMatched=:matched WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':matched'] = $matched;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
	}
	
	public function setLastRow($lastChecked)
	{
		$table = $this->getTableName();
		
		$sql = "UPDATE $table SET lastRow=:lastRow WHERE objectID=:objectID";
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
	
	//Images
	public function deleteImage()
	{
		require_once('/var/www/html/api/image/obj/Image.php');
		$Image = new Image($this->row['imageID']);

		$Image->delete();
		$this->setImage('');
	}
	
	public function setImage($imageID)
	{
		$sql = "UPDATE user SET imageID=:imageID WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':imageID'] = $imageID;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
	}
}

?>
