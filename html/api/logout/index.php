<?
	/*
	filename: /html/api/logout/index.php
	parameters: 
		$_POST['email'] = user's email
		$_POST['password'] = user's password
		
	description:
		Get the user by the password, see if it uses
		the password
	*/
	include_once('/var/www/html/src/php/setup.php');
	//include_once('/var/www/html/api/user/obj/User.php');
	
	//Build response	
	$response = array();
	$response[0]['time'] = time();
	$response[0]['status'] = 0;
  
	//Destroy any sessions
	session_start();

		//Unset any session variables
		unset($_SESSION['userID']);

	session_destroy();
	
	//Set the status to 1 (success)
	$response[0]['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>