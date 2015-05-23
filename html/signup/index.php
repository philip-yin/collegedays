<?
	session_start();
			
	if(isset($_SESSION['userID']) && $_SESSION['userID'] != '')
		header('Location: http://gocollegedays.com');
			
		include_once('/var/www/html/src/php/setup.php');
		
		$title = "Signup";
		$content = '/var/www/html/src/html/signup/signup.html';
		require_once('/var/www/html/src/html/blank.html');
		
		
?>
