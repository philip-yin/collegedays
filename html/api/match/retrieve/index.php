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
	
	//Default match ID
	$matchID = '';
	
	if(isset($_POST['match']))
		$matchID = $_POST['match']; //A match was provided
	else
	{
		//No match ID provided, get the user's current match
		$matchID = $User->getCurrentMatchID();
	}

	//Make a match
	$Match = new Match($matchID);
	
	//Make a connection
	$PDOconn = newPDOconn();
	
	//Get the rows in the table
	$sql = "SELECT COUNT(*) FROM user";
	$stmtA = $PDOconn->prepare($sql);
	$stmtA->execute();
	$rowsintable = $stmtA->fetchColumn();
	
	//echo "rows: ".$rowsintable."<br>";
	
	//If the matchID is empty and the match doesn't exist
	if($matchID == '' && !$Match->exists)
	{
		//Set the user to not matched
		$User->setMatched(false);
	
		//Get the last checked row
		$lastRow = $User->row['lastRow'];
		$nextRow = $lastRow++;

		//Skip if self
		if($nextRow == $User->row['row'])
			$nextRow++;
			
		$stillMatching = true;
		while($stillMatching)
		{
			if($nextRow > $rowsintable)
			$nextRow = 0;
		
			$nextUser = new User($nextRow, $PDOconn);
			$nextUser->refreshMatched();
			
			//echo "Checking user at row ".$nextRow." ";
			//echo " exists: ".$nextUser->exists."<br>";
			if($nextUser->exists)
			{
				//Found someone to match with
				if ($nextUser->row['currentlyMatched'] == 0 && ! $Match->areMatched($User->ID, $nextUser->ID))
				{
					$newMatch = new Match();
					$newMatch->create($User->ID, $nextUser->ID);
					
					$User->setMatched(true);
					$User->setLastRow($nextRow);
					$nextUser->setMatched(true);
					
					//echo("Created match...");
					$Match = $newMatch;
					
					$stillMatching = false;
				}
				else
				{
					//echo " not a match... <br>";
				}
				
			}
			
			if ($count > $rowsintable)
			$stillMatching = false;
			
			$nextRow++;
			$count++;
		} 
	}
	else
	{
		//The user is matched
		$User->setMatched(true);
	}

	//Add this match to data
	$response['data']['matchID'] = $Match->ID;
	
	//Set the status to 1 (success)
	$response['meta']['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>