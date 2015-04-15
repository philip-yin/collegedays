<?
include_once('/var/www/html/api/obj/CDObject.php');

class User extends CDObject
{
	//Class constructor
	function User($identifier = NULL)
	{
		//Call super constructor
		parent::CDObject('user');

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
	}

	public function create($fName, $lName, $email, $password)
	{		
		if($this->exists)
			return false; //Already a user
		
		//See if email is valid
		if(!( filter_var($email, FILTER_VALIDATE_EMAIL)))
			return false; //Invalid email
		
		//See if the password is valid (4 characters)
		if(strlen($password) < 4)
			return false;
		
		//Check for an edu email
		$emailLength = strlen($email);
		$emailDomain = '';
		$i = $emailLength;
		while($i > 0)
		{
			if($email[$i] == '.')
				break;
				
			$emailDomain .= $email[$i--];
		}
		
		if($emailDomain != "ude") //edu backwards
			return false;
		
		//Valid data
		//Create a password salt and hash
		$passSalt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$passHash = hash('sha256', $password.$passSalt);
				
		//Create user
		$sql = "INSERT INTO user (objectID, email, fName, lName, passHash, passSalt) VALUES
								 (:objectID, :email, :fName, :lName, :passHash, :passSalt)";
		$stmtI = $this->PDOconn->prepare($sql); 
		$paramsI[':objectID'] = $this->generateUniqueID();
		$paramsI[':email'] = $email;
		$paramsI[':fName'] = $fName;
		$paramsI[':lName'] = $lName;
		$paramsI[':passHash'] = $passHash;
		$paramsI[':passSalt'] = $passSalt;
		$successI = $stmtI->execute($paramsI);
		
		return $successI;
	}
	
	
}

?>