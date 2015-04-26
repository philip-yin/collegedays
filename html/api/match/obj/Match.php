<?
include_once('/var/www/html/api/obj/CDObject.php');
include_once('/var/www/html/api/user/obj/User.php');

class Match extends CDObject
{
	//Class constructor
	function Match($identifier = NULL)
	{
		//Call super constructor
		parent::CDObject('mach');

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
	
	public function areMatched($userID_a, $userID_b)
	{
		$sql = "SELECT * FROM mach WHERE (userID_a = :userID_a1 OR userID_b = :userID_b1)
				OR (userID_a = :userID_b2 OR userID_b = :userID_a2)";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':userID_a1'] = $userID_a;
		$paramsA[':userID_a2'] = $userID_a;
		$paramsA[':userID_b1'] = $userID_b;
		$paramsA[':userID_b2'] = $userID_b;
		$stmtA->execute($paramsA);
	
		if($stmtA->rowCount() > 0)
			return true;
			
		return false;
	}
	
	//Create a new match between the two users, returns the matchID of the newly created match
	public function create($userID_a = '', $userID_b = '')
	{	
		//Ensure this match doesn't exist
		if($this->exists)
			return false;
		
		//Ensure the users exist
		$User_a = new User($userID_a);
		$User_b = new User($userID_b);
		
		if(!$User_a->exists || !$User_b->exists)
			return false;
			
		//Ensure the users are not friends
		
		//Insert match into table
	
		//Increment the user's match count
		
		//Return the new matchID
		return '';
	}
	
}

?>