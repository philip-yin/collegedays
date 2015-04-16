<html>
<head>
	<link rel="stylesheet" type="text/css" href="/css/basic.css">
	<link rel="stylesheet" type="text/css" href="/css/landing.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
	<div id="mainContainer" class="ninesixty_container">
		<?
			session_start();
			include_once('/var/www/html/src/php/setup.php');
			include_once('/var/www/html/api/user/obj/User.php');

			if(isset($_SESSION['userID']))
			{
				$User = new User($_SESSION['userID']);
				echo "Welcome, ".$User->row['fName']." ".$User->row['lName']."!<br><br>";
				echo "<a href='http://gocollegedays.com/api/logout/'>Logout</a>";
			}
			else
			{
				echo "CollegeDays";
				echo "<form method='POST' action='http://gocollegedays.com/api/login/'>";
				echo "<input type='text' name='email'><br>";
				echo "<input type='password' name='password'><br>";
				echo "<input type='submit' value='Login'>";
				echo "</form>";
			}			

		?>

	</div>
</body>
</html>
