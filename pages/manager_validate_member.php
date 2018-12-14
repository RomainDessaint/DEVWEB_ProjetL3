<?php
include('../includes/functions.inc.php');
session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title> Manager - Demandes de ralliement </title>
</head>

<body>
	<header>
	</header>

	<section>
		<h1> Manager - Demandes de ralliement </h1>
		<?php
		if(accountIsConnected()) {
			if(managerIsConnected()) {
				echo awaitingValidationMembers();
				echo validatingMembers();
			} else {
				echo('Vous n\'êtes pas autorisé à accèder à cette page.');
			}
		} else {
			echo('Veuillez vous connecter pour accèder à cette page.');
			echo displayLogButton();
			echo logButton();
		}
		echo displayBackButton();
		echo backButton('manager_logged.php');
		?>
	</section>
</body>
</html>
