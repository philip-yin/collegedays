<?
include_once('/var/www/html/api/obj/CDObject.php');

class User extends CDObject
{
	public User($identifier = NULL)
	{
		//Call super constructor
		parent::CDObject('user');
	
		//Try to identify the user
		if($identifier = NULL)
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
	
	public getName()
	{
		$name = array();
		$name['fName'] = $this->row['fName'];
		$name['lName'] = $this->row['lName'];
		$name['fullName'] = $name['fName']." ".$name['lName'];
		
		return $name;
	}
}

?>