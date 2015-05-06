<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="/css/basic.css">
	<link rel="stylesheet" type="text/css" href="/css/landing.css">
	<link href='http://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>
	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
	<div id="mainContainer" class="ninesixty_container">
		<?
			session_start();
			
			if(isset($_SESSION['userID']) && $_SESSION['userID'] != '')
				header('Location: http://gocollegedays.com');
			
			include_once('/var/www/html/src/php/setup.php');
			include_once('/var/www/html/src/html/signup.html');
		?>

	</div>
	<div id="cdbear">
	</div>
</body>
</html>
