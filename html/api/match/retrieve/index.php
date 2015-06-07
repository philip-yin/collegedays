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
	
	$testing = false;
	if((int)$_GET['test'] == 1 && TRUE)
	{
		$_SESSION['userID'] = $_GET['user'];
		echo "<br>SESSION FOR USER: ".$_SESSION['userID']."<br>";
		$User = new User($_SESSION['userID']);
		$User->istest = true;
		$testing = true;
	}
	else if(!isset($_SESSION['userID']))
	{
		//Send the response
		$response['data']['reason'] = "The user is not logged in.";
		sendResponse(400, json_encode($response));
		return false;
	}
	
	//Get the current user
	if(!isset($User))
		$User = new User($_SESSION['userID']);
	
	//Default match ID
	$matchID = '';
	
	if(isset($_POST['match']))
		$matchID = $_POST['match']; //A match was provided
	else
	{
		//No match ID provided, get the user's current match
		$matchID = $User->getCurrentMatchID();
		
		if($matchID != '')
		{
			//Add this match to data
			$response['data']['matchID'] = $matchID;
			$response['meta']['status'] = 1;
			sendResponse(200, json_encode($response)); return false;
		}
	}

	//Make a connection
	$PDOconn = newPDOconn();
	
	//Make a match
	$Match = new Match($matchID, $PDOconn);
	
	//Get the rows in the table
	$tablename = "user";
	if($testing) $tablename .= "_test";
	
	$sql = "SELECT row FROM ".$tablename." ORDER BY row DESC LIMIT 1";
	$stmtA = $PDOconn->prepare($sql);
	$stmtA->execute();
	$row = $stmtA->fetch();
	$maxRow = $row['row'];
	
	$debug = false;
	// if($debug) echo "rows: ".$rowsintable."<br>";
	$nextRow = 0;
	//If the matchID is empty and the match doesn't exist
	if($matchID == '' && !$Match->exists)
	{
		//Set the user to not matched
		$User->setMatched(false);
	
		//Get the last checked row
		$lastRow = $User->row['lastRow'];
		$nextRow = $lastRow++;

		//making a PQ
		$PQ = new CDPriorityQueue();

		$stillMatching = true;
		while($stillMatching)
		{
			//Skip if self
			if($nextRow == $User->row['row'])
			$nextRow++;
			
			if($nextRow > $maxRow)
			$nextRow = 0;
		
			$nextUser = new User($nextRow, $PDOconn);
			if($testing) $nextUser->istest = true;
			$nextUser->refreshMatched();
			
			if($nextUser->exists)
			{
				$matchCount = $Match->countMatches($User->ID, $nextUser->ID);

				//Found someone to match with
				if ($matchCount == 0 && $nextUser->row['currentlyMatched'] == 0 )//&& !$Match->areMatched($User->ID, $nextUser->ID))
				{
					if($debug) echo "Creating a match with ".$nextUser->row['fName']." (".$nextRow.") <br>";
					$newMatch = new Match(NULL, $PDOconn);
					
					if($testing) $newMatch->istest = true;
					
					$newMatchResult = $newMatch->create($User->ID, $nextUser->ID);
					if($debug) echo "Match creation result: ".$newMatchResult."<br> was test: ".$newMatch->istest;
					
					$User->setMatched(true);
					$User->setLastRow($nextRow);
					$nextUser->setMatched(true);
					
					$Match = $newMatch;
					
					$stillMatching = false;
				}
				else if($nextUser -> row['currentMatched'] == 0)//if they are not matched and we could not match them
				{
					if($debug) echo "Inserting ".$nextUser->row['fName']." with priority of ".$matchCount."<br>";
					$PQ -> insert($nextUser -> ID, $matchCount);
				}
			}
			else
			{
				// if($debug) echo " didnt exist...";
			}
			
			if ($count > $maxRow)
			 $stillMatching = false;
			
			$count++;
			$nextRow++;
		} 
	}
	else
	{
		//The user is matched
		$User->setMatched(true);
	}

    if($Match->ID == '' && $PQ->count() > 0)
    {
    	$matchingUserID = $PQ->top();
    	$newUser = new User($matchingUserID, $PDOconn);
		
    	if($debug) echo "Popped ".$newUser->row['fName'].", making a match...";
		$newMatch = new Match(NULL, $PDOconn);
		$newMatch->istest = true;
		// if($debug) echo "<br>new match is test: ".$newMatch->istest;
		$newMatch->create($User->ID, $matchingUserID);
		if($debug) echo "Match created... ";
					
		$User->setMatched(true);
		$User->setLastRow($nextRow);
		$newUser->setMatched(true);
					
		$Match = $newMatch;
    }


	//Add this match to data
	$response['data']['matchID'] = $Match->ID;
	
	//Set the status to 1 (success)
	$response['meta']['status'] = 1;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>