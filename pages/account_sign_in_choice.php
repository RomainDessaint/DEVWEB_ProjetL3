<?php
include('../includes/functions.inc.php');
session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title> Choix d'inscription </title>
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
			echo signInChoiceForm();
			echo signInChoice();
		}
		echo displayBackButton();
		echo backButton('account_log_in.php');
		?>
	</section>
</body>
</html>
