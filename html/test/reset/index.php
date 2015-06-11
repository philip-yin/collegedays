<?

include_once('/var/www/html/src/php/setup.php');

$PDOconn = newPDOconn();

return false;

$sql = "UPDATE mach SET creationTime='0'";
$stmtA = $PDOconn->prepare($sql);
$stmtA->execute();


?>