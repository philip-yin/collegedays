<?
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/user/obj/User.php');
	
	$response = array();
	$response['data'] = array();
	
		//Create a meta object
		$meta = array();
		$meta['time'] = time();
		$meta['type'] = '/account/preferences/';
		$meta['status'] = 0;

	//Add the meta
	$response['meta'] = $meta;
	
		//Logged in?
		session_start();
		if(!isset($_SESSION['userID']))
		{
			$response['data']['reason'] = "Login required.";
			sendResponse(400, json_encode($response)); return false;
		}
	
	//Connect
	$PDOconn = newPDOconn();
	$User = new User($_SESSION['userID'], $PDOconn);
	
	$User->verifyEmail();

	//Set the status to 1 (success)
	$response['meta']['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>