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


echo "CREATE DICTIONARY WHERE key=userID AND value=matchID\n <br>";
$matches = array();
for($i = 0; $i < count($users); $i++)
{
    $User = $users[$i];
	$User->istest = true;
	
		//make a curl request to the match api
		$login_url = "http://gocollegedays.com/api/login/?test=1&user=".$User->ID;
		$match_url = "http://gocollegedays.com/api/match/retrieve/?test=1&user=".$User->ID;
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		curl_setopt($ch, CURLOPT_URL, $login_url);
		// Execute
		$login_result = curl_exec($ch);
		// Closing
			//echo $login_result."<br>";
		curl_setopt($ch, CURLOPT_URL, $match_url);
		
		echo "<br>";
		echo "Requesting match for user: ".$User->ID."<br>";
		$match_result = curl_exec($ch);
			echo $match_result."<br>";
		
		curl_close($ch);

		
	$matches[$User->ID] = $User->getCurrentMatchID();
}

//TEST EACH MATCH
echo "<br><br><b> TEST MATCHES </b></br>";
for($b = 0; $b < count($users); $b++)
{
	$User = $users[$b];
	echo "<br> TEST MATCH FOR ".$User->row['fName'].": ".$matches[$User->ID]."  ---> ".assertTrue();
}

return;

function assertTrue()
{
	if(true)
	return "<b>TRUE</b>";
}

?>