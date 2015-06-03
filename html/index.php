<?
	session_start();
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/match/obj/Match.php');
	
	//Where are we
	$isHome = true; $isFriends = $isYou = false;

	//Trying to verify?
	if(isset($_GET['key']) && isset($_GET['user']))
	{
		$User = new User($_GET['user']);
		if(!$User->exists) return false;
		
		if($User->verify($_GET['key']))
			header('Location: http://gocollegedays.com');
	}
	
	if(isset($_SESSION['userID']))
	{
		$User = new User($_SESSION['userID']);
		$Viewer = $User;
		
		//Is matching?
		if($User->row['matching'] > 0 && $User->isVerified())
		{
			$matchID = $User->getCurrentMatchID();
			
			if($matchID != '')
				$content = '/var/www/html/src/html/home/home.html';
			else
				$content = '/var/www/html/src/html/home/unmatched.html';

			$title = 'Home';
			require_once('/var/www/html/src/html/blank.html');
			echo "<script src='/src/js/logout.js'></script>";	
		}
		else
		{
			$title = 'Home';
			$content = '/var/www/html/src/html/preferences.html';
			require_once('/var/www/html/src/html/blank.html');	
		}
	}
	else
	{
		$content = '/var/www/html/src/html/login/login.html';
		require_once('/var/www/html/src/html/blank.html');
	}

?>
