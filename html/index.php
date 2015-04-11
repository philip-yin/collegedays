<html>
<head>
	<link rel="stylesheet" type="text/css" href="/css/basic.css">
	<link rel="stylesheet" type="text/css" href="/css/landing.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
	<div id="mainContainer" class="ninesixty_container">
		<h1>Group 10</h1>
		<p>This is the landing page for group 10, welcome to it.</p>
		<br>
		<br>
		<? //Start using php with this tag
		phpinfo();
		//Variables start with $ and don't require a datatype
		$a = "apple";
		$b = "banana";
		$c = "kitty";

		//Use echo to output HTML
		echo "<h1>".$a." ".$b." ".$c."</h1>";

		//Arrays
		$fruits = array();

		$fruits[ count($fruits) ] = $a; //count($fruits) = 0
		$fruits[ count($fruits) ] = $b; // 1
		$fruits[ count($fruits) ] = $c; // 2
		$fruits[ 3 ] = "orange";

		for($i = 0; $i < count($fruits); $i++)
		{
			echo $fruits[$i]."<br>";
		}

		//Stop using php with this tag
		?>
	</div>
</body>
</html>
