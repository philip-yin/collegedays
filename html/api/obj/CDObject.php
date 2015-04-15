<?
//Include connection
include_once('/var/www/html/src/setup.php');

class CDObject
{
	public $ID;
	public $exists;
	public $tablename;
	public $row;
	public $PDOconn;
	
	function CDObject($tablename = '')
	{
		$this->PDOconn = newPDOconn();
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