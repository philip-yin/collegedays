<?
//Creates a new PHP data object connection to the database
function newPDOconn($readonly = false)
{
	//DB reader
	$DBUser = 'cdaccess';

	//If read only
	if($readonly)
	{
		$DBUser = 'cdread';
	}
	
	//RDS address
	$DBServer = 'gtdata.c3wwdzbp6vw7.us-west-2.rds.amazonaws.com';
	$DBPass   = 'CollegeDays!';
	$DBName   = 'cd_data';

	$dsn = 'mysql:dbname='.$DBName.';host='.$DBServer;

	//Make a connection
	try
	{
		$PDOconn = new PDO($dsn, $DBUser, $DBPass);
	}
	catch(Exception $e)
	{
		return NULL;
	}	
	
	//Check prepared statements for correct parameters
	$PDOconn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$PDOconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	return $PDOconn;
}
?>