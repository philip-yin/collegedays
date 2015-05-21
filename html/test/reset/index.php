<?

include_once('/var/www/html/src/php/setup.php');

$PDOconn = newPDOconn();

$sql = "UPDATE mach SET creationTime='0'";
$stmtA = $PDOconn->prepare($sql);
$stmtA->execute();


?>