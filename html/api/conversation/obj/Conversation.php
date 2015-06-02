<?
require_once('/var/www/html/api/obj/CDObject.php');

class Conversation extends CDObject
{
	//Class constructor
	function Conversation($identifier = NULL, $PDOconn = NULL)
	{
		//Call super constructor
		parent::CDObject('conversation', $PDOconn);

		//Try to identify the user
		if($identifier == NULL)
			return;
			
			//First try to search by ID
			$sql = "SELECT objectID FROM conversation WHERE objectID=:objectID";
			$stmtA = $this->PDOconn->prepare($sql);
			$paramsA[':objectID'] = $identifier;
			$stmtA->execute($paramsA);
			
			//Any convos by id?
			if($stmtA->rowCount() > 0)
			{
				$row = $stmtA->fetch();
				$this->ID = $row['objectID'];
				$this->exists = true;
				$this->refresh();
				return; //Done
			}

			//Try by row
			$sql = "SELECT objectID FROM conversation WHERE row=:row";
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

	public function findConversation($userID_a, $userID_b)
	{
		if($this->exists) return false;
		
		$sql = "SELECT conversationID FROM inconvo WHERE 
				((userID_a=:userID_a1 AND userID_b=:userID_b1) OR (userID_a=:userID_b2 AND userID_b=:userID_a2))";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':userID_a1'] = $userID_a;
		$paramsA[':userID_b1'] = $userID_b;
		$paramsA[':userID_b2'] = $userID_b;
		$paramsA[':userID_a2'] = $userID_a;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() == 0) return false;
		
		$row = $stmtA->fetch();
		$this->ID = $row['conversationID'];
		$this->exists = true;
		$this->refresh();
		return;
	}
	
	public function create($userID_a, $userID_b)
	{
		if($this->exists)
			return false;
		
		//Do the users exist?
		$User_a = new User($userID_a); if(!$User_a->exists) return false;
		$User_b = new User($userID_b); if(!$User_b->exists) return false;
		
		//Create conversation
		$uniqueID = $this->generateUniqueID();
		$sql = "INSERT INTO conversation  (objectID) VALUES (:objectID)";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':objectID'] = $uniqueID;
		$stmtA->execute($paramsA);
		
		//Add these users to the conversation
		$sql = "INSERT INTO inconvo (userID_a, userID_b, conversationID) VALUES (:userID_a, :userID_b, :conversationID)";
		$stmtB = $this->PDOconn->prepare($sql);
		
		$paramsB[':userID_a'] = $userID_a;
		$paramsB[':userID_b'] = $userID_b; 
		$paramsB[':conversationID'] = $uniqueID; 
		
		//Add both to the conversation
		$stmtB->execute($paramsB);
		
			$this->ID = $uniqueID;
			$this->refresh();
			$this->exists = true;

		return true;
	}
}

?>
