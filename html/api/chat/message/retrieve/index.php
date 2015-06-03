<?
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/user/obj/User.php');
	
	$response = array();
	$response['data'] = array();
	
		//Create a meta object
		$meta = array();
		$meta['time'] = time();
		$meta['type'] = '/chat/message/retrieve/';
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
	
	//Get the values
	$conversationID = $_POST['conversation'];
	$newerThan = (int)$_POST['newer_than'];
	$newFirst = (int)$_POST['newFirst'];
	
	if($newFirst != 1) $newFirst = 0;

	if(!isset($newerThan) || $newerThan == 0) $newerThan = NULL;
	
	//OK
	require_once('/var/www/html/api/conversation/obj/Conversation.php');
	$Conversation = new Conversation($conversationID, $PDOconn);
	if(!$Conversation->exists)
	{
		$response['data']['reason'] = "The conversation doesn't exist.";
		sendResponse(400, json_encode($response)); return false;
	}

	$messagesResponse = $Conversation->getMessages($newerThan, NULL, $newFirst);	
	
	//Set the status to 1 (success)
	$response['meta']['status'] = $messagesResponse[0]['status'];
	$response['data']['messages'] = $messagesResponse[1]['messages'];
	$response['data']['newerThan'] = $newerThan;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>