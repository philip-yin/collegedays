<?
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/user/obj/User.php');

	//Create the response
	$response = array();
	$response['data'] = array();
	
		//Create a meta object
		$meta = array();
		$meta['time'] = time();
		$meta['type'] = '/account/disable/';
		$meta['status'] = 0;

	//Add the meta
	$response['meta'] = $meta;
	
	//Ensure the user is logged in
	session_start();
	if(!isset($_SESSION['userID']))
	{
		//Send the response
		$response['data']['reason'] = "The user is not logged in.";
		sendResponse(400, json_encode($response)); return false;
	}
	
	//Get the current user
	$User = new User($_SESSION['userID']);

	$password = $_POST['password'];
	
	if(!isset($password))
		$password = $_GET['password'];
	
	$disableResponse = $User->disableAccount($password);
	
	//Set the status to 1 (success)
	$response['meta']['status'] = (int)$disableResponse[0]['status'];
	$response['data']['reason'] = $disableResponse[1]['reason'];

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>