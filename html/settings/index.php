<?
	//Make a connection
	require_once('/var/www/html/src/php/setup.php');
	if(!isset($PDOconn))
		$PDOconn = newPDOconn();
	
	//logged in?
	$domain = CDConsts::getAPIDomain();
	
	session_start();
	if(!isset($_SESSION['userID']))
	{
		//No
		header('Location: '.$domain);
		return false;
	}
	
	//Make user
	if(!isset($User))
	$User = new User($_SESSION['userID'], $PDOconn);
	$Viewer = $User;
	
	$title = 'Settings';
	$content = '/var/www/html/src/html/settings/settings.html';
	require_once('/var/www/html/src/html/blank.html');
?>