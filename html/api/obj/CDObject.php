<?
//Include connection
include_once('/var/www/html/src/setup.php');

class CDObject
{
	public $ID;
	public $exists;
	
	private $tablename;
	private $row;
	private $PDOconn;
	
	public CDObject($tablename = '')
	{
		$this->PDOconn = newPDOconn();
		$this->ID = '';
		$this->tablename = $tablename;
		$this->exists = false;
	}
	
	public refresh()
	{
		//Get the row
		$sql = "SELECT * FROM :tablename WHERE objectID= :objectID LIMIT 1";		
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':tablename'] = $this->tablename;
		$paramsA[':objectID'] = $this->ID;
		$stmtA->execute($paramsA);
		
		if($stmtA->rowCount() == 0)
			return false;
		
		//Set the row
		$this->row = $stmtA->fetch();
	}
}

?>