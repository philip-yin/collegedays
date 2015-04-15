<?
//phpinfo();
include_once('/var/www/html/api/user/obj/User.php');

$email = 'c1jordan@ucsd.edu';

//Try to identify the user by email
$User = new User($email);

//Does the user exist?
if($User->exists)
	echo 'A user with email '.$email.' exists';
else
	echo 'A user with email '.$email.' does not exist';
	
echo '<br><br>The user id is '.$User->ID;
echo ' '.$User->row['fName'].' '.$User->row['lName'];
?>