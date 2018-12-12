<?php
include('../includes/functions.inc.php');
session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title> Inscription </title>
</head>

<body>
	<header>
	</header>

	<section>
		<h1> S'inscire </h1>
		<?php
		if(accountIsConnected()) {
			echo('Vous êtes déja inscrit.');
		} else {
			echo signInForm();
			echo signIn();
		}
		echo displayBackButton();
		echo backButton('account_sign_in_choice.php');
		?>
	</section>
</body>
</html>
