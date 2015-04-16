<?
	/*
	filename: /html/api/user/create/index.php
	parameters: 
		$_POST['fName'] = user's first name
		$_POST['lName'] = user's last name
		$_POST['email'] = user's email
		$_POST['password'] = user's password
		
	description:
		Check to see if a user exists already using the email,
		if not, create a new user
	*/
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/user/obj/User.php');
	
	$response = array();
	$response[0]['time'] = time();
	$response[0]['status'] = 0;
  
	//Get the lat and lng provided via GET http://gocollegedays.com/api/user/index.php?fName=Jick&lName=Joss
	$fName = $_POST['fName'];
	$lName = $_POST['lName'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	//If no values from POST, get values from GET
	if(!isset($fName) || !isset($lName) || !isset($email) || !isset($password))
	{
		$fName = $_GET['fName'];
		$lName = $_GET['lName'];
		$email = $_GET['email'];
		$password = $_GET['password'];
	}

	//Does a user exist already?
	$User = new User($email);
	
	if($User->exists)
	{
		$response[1]['reason'] = "A user with this email already exists.";
		sendResponse(400, json_encode($response));
		return false;
	}
	
	//The user doesn't exist yet
	if(! $User->create($fName, $lName, $email, $password))
	{
		$response[1]['reason'] = "The user couldn't be created.";
		sendResponse(400, json_encode($response));
		return false;
	}
	
	//Set the status to 1 (success)
	$response[0]['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>