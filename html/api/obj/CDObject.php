<?
//Include connection
include_once('/var/www/html/src/php/setup.php');

class CDObject
{
	public $ID;
	public $exists;
	public $tablename;
	public $row;
	public $PDOconn;
	
	function CDObject($tablename = '', $PDOconn = NULL)
	{
		if($PDOconn == NULL)
			$this->PDOconn = newPDOconn();
		else
			$this->PDOconn = $PDOconn;
			
		$this->ID = '';
		$this->tablename = $tablename;
		$this->exists = false;
	}
	
	public function refresh()
	{
		//Get the row
		if(!$this->isTablenameValid($this->tablename))
			return;
		
		$tablename = $this->tablename;
		$sql = "SELECT * FROM $tablename WHERE objectID= :objectID ";		
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() == 0)
			return false;
		
		//Set the row
		$this->row = $stmtA->fetch();
	}
	
	//Generates an objectID that isn't in the table
	public function generateUniqueID()
	{
		if(!$this->isTablenameValid($this->tablename))
			return false;
		
		$tablename = $this->tablename;
		while(true)
		{
			$objectID = $this->tablename.'_'.CDTools::randString(32);
			$sql = "SELECT objectID FROM $tablename WHERE objectID=:objectID";
			$stmtA = $this->PDOconn->prepare($sql);
			$paramsA[':objectID'] = $objectID;
			
			if($stmtA->rowCount() == 0)
				return $objectID;
				
			unset($stmtA);
			unset($paramsA);
		}
	}
	
	private function isTablenameValid()
	{
	    $sql = "SELECT tablename FROM validtable WHERE tablename=:tablename";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':tablename'] = $this->tablename;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() > 0)
			return true;
			
		return false;
	}
}

?>