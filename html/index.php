<?
	session_start();
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/match/obj/Match.php');
	
	//Where are we
	$isHome = true; $isFriends = $isYou = false;
	
	if(isset($_SESSION['userID']))
	{
		$User = new User($_SESSION['userID']);
		$Viewer = $User;
		
		$matchID = $User->getCurrentMatchID();
		
		if($matchID != '')
		{
			$content = '/var/www/html/src/html/home/home.html';
		}
		else
		{
			$content = '/var/www/html/src/html/home/unmatched.html';
		}

		require_once('/var/www/html/src/html/blank.html');
		//echo "<div id='logoutcontainer'><div id='logoutlink' href='http://gocollegedays.com/api/logout/'>Logout</div></div>";
		echo "<script src='/src/js/logout.js'></script>";		
	}
	else
	{
		$content = '/var/www/html/src/html/login/login.html';
		require_once('/var/www/html/src/html/blank.html');
	}

?>
