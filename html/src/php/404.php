<?

$URL = $_SERVER["REQUEST_URI"];

//echo $URL."<br>";

//Remove the first /
if($URL[0] == "/")
	$URL = substr($URL, 1);

//Search for the restaurant
$searchString = "";
$view = "";

$i = 0;
$sCount = 0;
while($i < strlen($URL))
{
  if($URL[$i] == "/")
	$sCount++;
  else
  {
	  if($sCount == 0)
		$searchString .= $URL[$i];
	  else if($sCount == 1)
		$view .= $URL[$i];
  }
  
  $i++;
}

//Format search string to rtag
$type = str_replace('-', '', $searchString);
$type = str_replace('_', '', $type);

//Search for the rTag
include_once('/var/www/html/src/php/setup.php');
$PDOconn = newPDOconn();

if($type == 'user')
{
	//DISPLAY THE RESTAURANT
	$userID = $view;
	$User = new User($userID);
	if($User->exists)
	{
		//Show profile
		require_once('/var/www/html/src/html/profile/profile.html');
		return true;
	}
	else
		pageNotFound();
}

function pageNotFound()
{
		echo "<span style='font-size:30'>404</span><br>";
		echo "Page not found.";
}
?>