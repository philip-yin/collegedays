<?
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/user/obj/User.php');
	
	$response = array();
	$response['data'] = array();
	
		//Create a meta object
		$meta = array();
		$meta['time'] = time();
		$meta['type'] = '/chat/message/send/';
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
	$body = $_POST['body'];

	if(!isset($body))
	{
		$response['data']['reason'] = "No body.";
		sendResponse(400, json_encode($response)); return false;
	}
	
	//Check body
	$body = strip_tags($body);
	
	//OK
	require_once('/var/www/html/api/conversation/obj/Conversation.php');
	$Conversation = new Conversation($conversationID, $PDOconn);
	if(!$Conversation->exists)
	{
		$response['data']['reason'] = "The conversation doesn't exist.";
		sendResponse(400, json_encode($response)); return false;
	}

	$Message = new Message(NULL, $PDOconn);
	$Message->send($User->ID, $Conversation->ID, $body);

	//Set the status to 1 (success)
	$response['meta']['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>