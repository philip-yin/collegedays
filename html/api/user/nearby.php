<?
/*
	filename: nearby.php
	parameters: 
		$_POST['lat']	the user's latitude
		$_POST['lng']	the user's longitude
	description:
		Takes the user's latitude and longitude and returns
		an array of names of people nearby
*/
	include_once('/var/www/html/src/setup.php');
  
	$response = array();
	$response[0]['time'] = time();
	$response[0]['status'] = 1;
  
	//Get the lat and lng provided via GET
	$lat = $_GET['lat'];
	$lng = $_GET['lng'];
  
	//Check for valid data
	if(!isset($lat) || !isset($lng) || !is_numeric($lat) || !is_numeric($lng)) //If the values are not set or aren't numbers
	{
		$response[1]['error'] = "Invalid latitude or longitude.";
		sendResponse(400, json_encode($response));
		return false;
	}

	//Return an array of nearby userIDs
	$userIDs = array();
	$userIDs[ count($userIDs) ] = "Bob";
	$userIDs[ count($userIDs) ] = "Sally";
	$userIDs[ count($userIDs) ] = "Billy";
  
	//Attach the data the response array
	$response[1]['data'] = $userIDs;
	
	//Set the status to 1 (success)
	$response[0]['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>