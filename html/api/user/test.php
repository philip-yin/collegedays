<?

include_once('/var/www/html/api/user/obj/User.php');

//Try to identify the user by email
$User = new User('c1jordan@ucsd.edu');

//Does the user exist?
if($User->exists)
	echo 'The user exists.';
else
	echo 'The user does not exist.';
?>