<?
	/*
	filename: /html/api/match/retrieve/index.php
	parameters: 
		$_POST['match'] = the ID of the match
		
	description:
		Retrieve the match by the provided ID, if the match
		doesn't exist or is not provided, provide the user's current match.
		If the user doesn't have a current match, make a new match.
	*/
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/user/obj/User.php');


	//Create the response
	$response = array();
	$response['data'] = array();
	
		//Create a meta object
		$meta = array();
		$meta['time'] = time();
		$meta['type'] = '/account/email/change/';
		$meta['status'] = 0;

	//Add the meta
	$response['meta'] = $meta;
	
	//Ensure the user is logged in
	session_start();
	if(!isset($_SESSION['userID']))
	{
		//Send the response
		$response['data']['reason'] = "The user is not logged in.";
		sendResponse(400, json_encode($response));
		return false;
	}
	
	//Get the current user
	$User = new User($_SESSION['userID']);
	
	$password = $_POST['password'];
	$original_email =$_POST['original_email'];
	$new_email = $_POST['new_email'];
	$confirm_new_email = $_POST['confirm_new_email'];

	$change_email_response = $User -> change_email($password, $original_email, $new_email, $confirm_new_email);

	//Set the status to 1 (success)
	$response['meta']['status'] = (int)$change_email_response[0]['status'];

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>