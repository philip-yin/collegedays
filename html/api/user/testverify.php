<?
require_once('/var/www/html/src/php/setup.php');
require_once('/var/www/html/src/php/mail/mail.php');

session_start();
$User = new User($_SESSION['userID']);
if(!$User->exists) return 'No';
$User->verifyEmail();

/*
$PDOconn = newPDOconn();
//Make link
		$verificationLink = "http://gocollegedays.com/?key=user=";
		
		//The source
		$sourceAddress = "CollegeDays <hello@gocollegedays.com>";
		
		//The subject header
		$subjectHeader = "Hello, !";
		
		//Body text
		$bodyText = 'Follow this link to verfy your erify Email</a>';
		
		//Body HTML
		$titleText = 'Please verify your ail!';
		$bodyHTML = 
		'<p>
		Follow the link below to verify your account:
		</p>
		<p>
		<a href=>Verify Email</a>
		</p>
		';
		
		$userEmail = 'c1jordan@ucsd.edu';
		
		//Send the email
		require_once('/var/www/html/src/php/mail/mail.php');
		sendEmail($sourceAddress, $userEmail, $subjectHeader, $titleText, $bodyText, $bodyHTML, $PDOconn);*/
?>