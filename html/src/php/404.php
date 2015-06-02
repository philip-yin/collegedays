<?
session_start();
$URL = $_SERVER["REQUEST_URI"];

//echo $URL."<br>";

//Remove the first /
if($URL[0] == "/")
	$URL = substr($URL, 1);

//gocollegedays.com/chat/user_ID
//Get all
$blocks = array();

$i = 0;
$sCount = 0;
while($i < strlen($URL))
{
  if($URL[$i] == "/")
	$sCount++;
  else
  {
	  $blocks[$sCount] .= $URL[$i];
  }
  
  $i++;
}

//Format search string to rtag
for($i = 0; $i < count($blocks); $i++)
{
	$blocks[$i] = str_replace('-', '', $blocks[$i]);
	
	if(!(preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $blocks[$i])))
	{
		pageNotFound(); return;
	}
}

// chat, user_ID
// user, user_ID

//OK, continue
include_once('/var/www/html/src/php/setup.php');
$PDOconn = newPDOconn();

if(count($blocks) == 2)
{
	if($blocks[0] == 'user')
	{
		//DISPLAY THE USER PROFILE
		$userID = $blocks[1];
		$User = new User($userID);
		
		$viewerID = $_SESSION['userID'];
		$Viewer = new User($viewerID);
		
		if($User->exists)
		{
			//Show profile
			$title = $User->row['fName']." ".$User->row['lName'];
			$content = '/var/www/html/src/html/profile/profile.html';
			require_once('/var/www/html/src/html/blank.html');
			return true;
		}
		else
			{pageNotFound(); return;}
	}
	else if($blocks[0] == 'chat')
	{
		//DISPLAY THE CHAT
		require_once('/var/www/html/api/conversation/obj/Conversation.php');
		$chattingWithUserID = $blocks[1];
		$Friend = new User($chattingWithUserID);
		
		if(!$Friend->exists)
			{ pageNotFound(); return; }
		
		//Friend exists, get their conversation
		$User =  new User($_SESSION['userID']);
		$Conversation = $Friend->getConversationWith($User->ID, true);

		if(!$Conversation->exists)
			{ pageNotFound(); return; }
			
		//Show their conversation
		$title = $User->row['fName']." ".$User->row['lName']." - Chat";
		$content = '/var/www/html/src/html/chat/conversation.html';
		require_once('/var/www/html/src/html/blank.html');
		return true;
	}
	else
		{pageNotFound(); return;}
}
else
	{pageNotFound(); return;}


return;

function pageNotFound()
{
		echo "<span style='font-size:30'>404</span><br>";
		echo "Page not found.";
}
?>