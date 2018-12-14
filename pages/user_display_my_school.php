<?php
include('../includes/functions.inc.php');
session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title> Utilisateur - Mon école </title>
</head>

<body>
	<header>
		<?php
		echo sessionInformation();
		?>
	</header>

	<section>
		<h1> Utilisateur - Mon école </h1>
		<?php
		if(accountIsConnected()) {
			if(userIsConnected()) {
				echo displayMySchool();
				echo displayChangeSchoolButton();
				echo changeSchoolForm();
				echo changeSchool();
				echo orgList();
				echo moreInformationOrg();
			} else {
				echo('Vous n\'êtes pas autorisé à accèder à cette page.');
			}
		} else {
			echo('Veuillez vous connecter pour accèder à cette page.');
			echo displayLogButton();
			echo logButton();
		}
		echo displayBackButton();
		echo backButton('user_logged.php');
		?>
	</section>
</body>
</html>
