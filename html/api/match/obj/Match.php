<?
include_once('/var/www/html/api/obj/CDObject.php');
include_once('/var/www/html/api/user/obj/User.php');

class Match extends CDObject
{

	//Class constructor
	function Match($identifier = NULL, $PDOconn = NULL)
	{
		//Call super constructor
		parent::CDObject('mach', $PDOconn);

		//Try to identify the user
		if($identifier == NULL)
			return;
			
			//First try to search by ID
			$sql = "SELECT objectID FROM mach WHERE objectID=:objectID";
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
	}

	//Find a possible match for the user and return a userID
	public function findMatchForUserID($userID_a = '')
	{
		//Get the user
		$User_a = new User($userID_a);
		
		//Does the user exist?
		if(! $User_a->exists)
			return false;
	
		//Find a user to match with
			//THIS IS WHERE WE GENERATE A MATCH
		
		//Create the match
		$this->create($userID_a, $userID_b);
	}

	public function sayHi($userID)
	{
		$User = new User($userID);
		$matchID = $User->getCurrentMatchID();
		
		$isA = $this->isA($userID);
		$isB = !$isA;
		
		$table = $this->getTableName();
		
		$sql = "UPDATE $table SET hi_a='1' WHERE objectID=:machID";
		if($isB) $sql = "UPDATE mach SET hi_b='1' WHERE objectID=:machID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':machID'] = $this->ID;
		return $stmtA->execute($paramsA);
	}
	
	public function isA($userID)
	{
		$table = $this->getTableName();
		
		$sql = "SELECT userID_a FROM $table WHERE objectID=:matchID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':matchID'] = $this->ID;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() == 0)
			return false;
			
		$row = $stmtA->fetch();
		if($row['userID_a'] == $userID)
			return true;
			
		return false;
	}
	
	public function isB($userID)
	{
		$table = $this->getTableName();
		
		$sql = "SELECT userID_b FROM $table WHERE objectID=:matchID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':matchID'] = $this->ID;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() == 0)
			return false;
			
		$row = $stmtA->fetch();
		if($row['userID_b'] == $userID)
			return true;
			
		return false;
	}
	
	public function secondsLeft()
	{
		if(!$this->exists)
			return 0;
			
		$secondsLeft = strtotime("tomorrow", time()) - time();
		
		//Return time until tomorrow
		return $secondsLeft;
	}
	
	public function areMatched($userID_a, $userID_b)
	{
		$User_a = new User($userID_a, $this->PDOconn);
		$User_b = new User($userID_b, $this->PDOconn);
		
		$matchID_a = $User_a->getCurrentMatchID();
		
		//echo "matchID_a: ".$matchID_a;
			
			if($matchID_a == '')
				return false;
		
		$matchID_b = $User_b->getCurrentMatchID();
		
		if($User_a->getCurrentMatchID() == $User_b->getCurrentMatchID())
			return true;
		
		
		
		return false;
	}

	public function countMatches($userID_a , $userID_b)
	{
		$table = $this->getTableName();

		$sql = "SELECT COUNT(*) FROM $table WHERE 
			(userID_a =:userID_a1 AND userID_b =:userID_b1) OR (userID_a =:userID_b2 AND userID_b =:userID_a2)";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':userID_a1'] = $userID_a;
		$paramsA[':userID_b1'] = $userID_b;
		$paramsA[':userID_a2'] = $userID_a;
		$paramsA[':userID_b2'] = $userID_b;
		$stmtA->execute($paramsA);
		
		return $stmtA->fetchColumn();

	}
	
	public function getNotUserID($userID)
	{
		if($this->row['userID_a'] == $userID)
			return $this->row['userID_b'];
		
		if($this->row['userID_b'] == $userID)
			return $this->row['userID_a'];
		
		return '';
	}	
	
	//Create a new match between the two users, returns the matchID of the newly created match
	public function create($userID_a = '', $userID_b = '')
	{
		//Ensure this match doesn't exist
		if($this->exists)  { return false;}
		
		//Ensure the users exist
		
		if($this->istest == false)
		{
			$User_a = new User($userID_a);
			$User_b = new User($userID_b);
			
			if(!$User_a->exists || !$User_b->exists)
				return false;
		}	
		//Ensure the users are not friends
		
		//Insert match into table
		//Create user
		$table = $this->getTableName();
		$uniqueID = $this->generateUniqueID();
		$sql = "INSERT INTO $table (objectID, userID_a, userID_b, creationTime) VALUES
								 (:objectID, :userID_a, :userID_b, :creationTime)";
		$stmtI = $this->PDOconn->prepare($sql);		
		$paramsI[':objectID'] = $uniqueID;
		$paramsI[':userID_a'] = $userID_a;
		$paramsI[':userID_b'] = $userID_b;
		$paramsI[':creationTime'] = time();
		$successI = $stmtI->execute($paramsI);
		
		if($successI)
		{
			$this->ID = $uniqueID;
			$this->refresh();
		}
		//Increment the user's match count
		
		//Return the new matchID
		return $uniqueID;
	}
	
}

?>