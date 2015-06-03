<?
require_once('/var/www/html/api/obj/CDObject.php');
require_once('/var/www/html/api/chat/message/obj/Message.php');

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

	public function getNotUserID($userID)
	{
		if(!$this->exists) return '';
		
		$sql = "SELECT userID_a, userID_b FROM inconvo WHERE conversationID=:conversationID LIMIT 1";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':conversationID'] = $this->ID;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() == 0)
			return '';
		
		while($row = $stmtA->fetch())
		{
			if($row['userID_a'] != $userID)
				return $row['userID_a'];
				
			if($row['userID_b'] != $userID)
				return $row['userID_b'];
		}
		
		return '';
	}
	
	public function getMessages($newerThan = NULL, $olderThan = NULL, $newFirst = 0)
	{
		$response[0]['status'] = 0;
		$response[1]['reason'] = '';
		
		if(!$this->exists)
		{
			$response[1]['reason'] = "No conversation";
			return $response;
		}
		
		$sql = "SELECT row FROM message WHERE conversationID=:conversationID";
			
			$m_index = -1;
			if($newerThan != NULL) 
			{
				$sql .= " AND time > :newerThan";
			}
			else
			{
				//Get the recent 10
				$messages = $this->row['messages'];
				$lastMessageIndex = $messages - 1;
				$startingMessageIndex = max(0, $lastMessageIndex - 10);
				$sql .= " AND m_index >= :m_index";
				$m_index = $startingMessageIndex;
			}
			
			if($olderThan != NULL) $sql .= " AND time < :olderThan";
		
			$orderBy = "ASC";
			if($newFirst > 0) $orderBy = "DESC";
		
		$sql .= " ORDER BY time ".$orderBy;
		//if($newFirst == 1 && $limit > 0) $sql .= " LIMIT ".(int)$limit;
		
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA = array();
		$paramsA[':conversationID'] = $this->ID;
		
		if($newerThan != NULL) $paramsA[':newerThan'] = $newerThan;
		if($m_index != -1) $paramsA[':m_index'] = $m_index;
		if($olderThan != NULL) $paramsA[':olderThan'] = $olderThan;

		$stmtA->execute($paramsA);
		
		$smallestIndex = NULL;
		$messages = array();
		while($messageRow = $stmtA->fetch())
		{
			$Message = new Message($messageRow['row']);
			if($smallestIndex == NULL || $Message->row['m_index'] < $smallestIndex)
				$smallestIndex = $Message->row['m_index'];
			
			$Message->disconnect();
			$messages[count($messages)] = $Message;
		}
		
		//Are there older messages?
		$hasOlder = 1;
		if($smallestIndex == 0)
			$hasOlder = 0;
		
		$response[0]['status'] = 1;
		$response[1]['hasOlder'] = $hasOlder;
		$response[1]['messages'] = $messages;
		
		return $response;
	}
	
	public function containsUserID($userID)
	{
		$sql = "SELECT conversationID FROM inconvo WHERE 
				((userID_a=:userID1 AND conversationID=:conversationID1) OR (userID_b=:userID2 AND conversationID=:conversationID2)) LIMIT 1";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':userID1'] = $userID;
		$paramsA[':conversationID1'] = $this->ID;
		$paramsA[':userID2'] = $userID;
		$paramsA[':conversationID2'] = $this->ID;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() == 1)
			return true;
			
		return false;
	}
	
	public function stepMessages($by)
	{
		if(!$this->exists) return false;
		
		$messages = $this->row['messages'];
		$messages += $by;
		
		$sql = "UPDATE conversation SET messages=:messages WHERE objectID=:objectID";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':messages'] = $messages;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
		$this->refresh();
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
