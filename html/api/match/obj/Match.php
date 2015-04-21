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
	public function find($userID_a = '')
	{
		//Get the user
		$User_a = new User($userID_a);
		
		//Does the user exist?
		if(! $User_a->exists)
			return false;
	
		//Find a user to match with
		
		
		//Create the match
		$this->create($userID_a, $userID_b);
	}
	
	//Create a new match between the two users
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
	}
	
}

?>