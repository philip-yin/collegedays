<?
	/*
	filename: /html/api/login/index.php
	parameters: 
		$_POST['email'] = user's email
		$_POST['password'] = user's password
		
	description:
		Get the user by the password, see if it uses
		the password
	*/
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/user/obj/User.php');
	
	$response = array();
	$response[0]['time'] = time();
	$response[0]['status'] = 0;
  
	//Get the lat and lng provided via GET http://gocollegedays.com/api/user/index.php?fName=Jick&lName=Joss
	$email = $_POST['email'];
	$password = $_POST['password'];

	//If no values from POST, get values from GET
	if(!isset($email) || !isset($password))
	{
		$email = $_GET['email'];
		$password = $_GET['password'];
	}

	//Get the user
	$User = new User($email);
	
	if(! $User->exists)
	{
		$response[1]['reason'] = "Incorrect password or email.";
		sendResponse(400, json_encode($response));
		return false;
	}
	
	//Correct password?
	if(! $User->usesPassword($password))
	{
		$response[1]['reason'] = "Incorrect password or email.";
		sendResponse(400, json_encode($response));
		return false;
	}
	
	//Successful login
	session_start();
	$_SESSION['userID'] = $User->ID;

	//Set the status to 1 (success)
	$response[0]['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>