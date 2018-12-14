<?php
include('../includes/functions.inc.php');
session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title> Administrateur - Gérer les écoles </title>
</head>

<body>
	<header>
		<?php
		echo sessionInformation();
		?>
	</header>

	<section>
		<h1> Administrateur - Gérer les écoles </h1>
		<?php
		if(accountIsConnected()) {
			if(adminIsConnected()) {
				echo displayCreateSchoolButton();
				echo createSchoolButton();
			} else {
				echo('Vous n\'êtes pas autorisé à accèder à cette page.');
			}
		} else {
			echo('Veuillez vous connecter pour accèder à cette page.');
			echo displayLogButton();
			echo logButton();
		}
		echo displayBackButton();
		echo backButton('admin_logged.php');
		?>
	</section>
</body>
</html>
