<?
require_once('/var/www/html/api/obj/CDObject.php');

class Message extends CDObject
{
	//Class constructor
	function Message($identifier = NULL, $PDOconn = NULL)
	{
		//Call super constructor
		parent::CDObject('message', $PDOconn);

		//Try to identify the user
		if($identifier == NULL)
			return;
			
			//First try to search by ID
			$sql = "SELECT objectID FROM message WHERE objectID=:objectID";
			$stmtA = $this->PDOconn->prepare($sql);
			$paramsA[':objectID'] = $identifier;
			$stmtA->execute($paramsA);
			
			//Any messages by id?
			if($stmtA->rowCount() > 0)
			{
				$row = $stmtA->fetch();
				$this->ID = $row['objectID'];
				$this->exists = true;
				$this->refresh();
				return; //Done
			}

			//Try by row
			$sql = "SELECT objectID FROM message WHERE row=:row";
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

	public function send($senderID, $conversationID, $message)
	{
		//Validate message
		if(strlen($message) == 0)
			return false;
	
		require_once('/var/www/html/api/conversation/obj/Conversation.php');
		$Sender = new User($senderID, $this->PDOconn);
		$Conversation = new Conversation($conversationID, $this->PDOconn);
		if(!$Conversation->containsUserID($Sender->ID)) return false;
		$Receiver = new User($Conversation->getNotUserID($Sender->ID), $this->PDOconn);

		//Both users are in this conversation
		
		//Create the message
		$uniqueID = $this->generateUniqueID();
		$sql = "INSERT INTO message (objectID, userID, conversationID, body, time, m_index) 
				VALUES (:objectID, :userID, :conversationID, :body, :time, :m_index)";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':objectID'] = $uniqueID;
		$paramsA[':userID'] = $Sender->ID;
		$paramsA[':conversationID'] = $Conversation->ID;
		$paramsA[':body'] = $message;
		$paramsA[':time'] = time();
		$paramsA[':m_index'] = $Conversation->row['messages'];
		
		//Increment messages count
		$Conversation->stepMessages(1);
		
		$stmtA->execute($paramsA);
	}
}

?>
