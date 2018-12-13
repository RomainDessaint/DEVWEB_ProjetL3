<?php
include('../includes/functions.inc.php');
session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title> Se connecter </title>
</head>

<body>
	<header>
	</header>

	<section>
		<h1> Se connecter </h1>
		<?php
		if(accountIsConnected()) {
			echo('Vous êtes déja connecté.');
		} else {
			echo logInForm();
			echo logIn();
			echo notRegisteredForm();
			echo notRegistered();
		}
		echo displayBackButton();
		echo backButton();
		?>
	</section>
</body>
</html>
