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
	
	$response['meta']['time'] = time();
	$response['meta']['status'] = 0;
  
	//Get the email and password
	$email = $_POST['email'];
	$password = $_POST['password'];

	//If no values from POST, get values from GET
	if(!isset($email) || !isset($password))
	{
		$email = $_GET['email'];
		$password = $_GET['password'];
	}

	//Get the user
	$testing = false;
	if((int)$_GET['test'] == 1 && TRUE)
	{
		//TESTING
		$User = new User($_GET['user']);
		$User->istest = true;
		$testing = true;
	}

	if(!isset($User))
		$User = new User($email);
	
	if(! $User->exists)
	{
		$response['data']['reason'] = "Incorrect password or email.";
		sendResponse(400, json_encode($response));
		return false;
	}
	
	//Correct password?
	if(!$testing && !$User->usesPassword($password))
	{
		$response['data']['reason'] = "Incorrect password or email.";
		sendResponse(400, json_encode($response));
		return false;
	}
	
	//Successful login
	session_start();
	$_SESSION['userID'] = $User->ID;

	//Set the status to 1 (success)
	$response['meta']['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>