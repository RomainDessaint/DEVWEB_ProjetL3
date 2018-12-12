<?php
include('includes/functions.inc.php');
session_start()
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title> Community Life Manager </title>
</head>

<body>
	<header>
		<?php
		echo sessionInformation('index');
		?>
	</header>

	<section>
		<h1> Accueil </h1>

		<?php
		echo displayGoButton();
		echo goButton();
		?>
	</section>
</body>
</html>
