<?

include_once('/var/www/html/src/php/setup.php');

//CREATE AN ARRAY OF ALL USERS IN THE TESTING TABLE
$PDOconn = newPDOconn();

$sql = "SELECT objectID FROM user";
$stmtA = $PDOconn->prepare($sql);
$stmtA->execute($paramsA);

$users = array();

$i = 0;
echo "ADD ALL USERS TO AN ARRAY users[] <br><br>\n";
while($userRow = $stmtA->fetch())
{
	$User = new User($userRow['objectID']);
	echo "---adding---> user_".$i.": ".$User->ID."<br>\n";
	
	$users[count($users)] = $User;
	
	$i++;
}

//CLEAR THE TEST MATCH TABLE
$sql = "TRUNCATE TABLE mach_test";
$stmtB = $PDOconn->prepare($sql);
$stmtB->execute();


echo "CREATE DICTIONARY WHERE key=userID AND value=matchID\n";
$matches = array();
for($i = 0; $i < count($users); $i++)
{
    $User = $users[$i];
	
}



?>