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
	
	//Create the response
	$response = array();
	$response['data'] = array();
	
		//Create a meta object
		$meta = array();
		$meta['time'] = time();
		$meta['type'] = 'match/retrieve';
		$meta['status'] = 0;

	//Ensure the user is logged in
		
	//By default
	$matchID = '';
	
	if(isset($_POST['match']))
	$matchID = $_POST['match'];

	
	
	//Set the status to 1 (success)
	$response['meta']['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>