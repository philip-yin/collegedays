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
	include_once('/var/www/html/api/match/obj/Match.php');
	include_once('/var/www/html/src/php/CDPriorityQueue.php');

	//Create the response
	$response = array();
	$response['data'] = array();
	
		//Create a meta object
		$meta = array();
		$meta['time'] = time();
		$meta['type'] = 'match/retrieve';
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
	
	$MatchID = $User->getCurrentMatchID();
	
	if($MatchID == '')
	{
		$response['data']['reason'] = "The user is currently unmatched";
		sendResponse(400, json_encode($response));
		return false;
	}
	
	//Create a match
	$Match = new Match($MatchID);
	
	$hiSuccess = $Match->sayHi($User->ID);

	//Set the status to 1 (success)
	$response['meta']['status'] = (int)$hiSuccess;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>