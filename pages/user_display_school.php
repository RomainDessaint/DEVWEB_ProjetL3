<?php
include('../includes/functions.inc.php');
session_start();
$school_name = getSchoolName();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title> Utilisateur - <?php echo $school_name ?> </title>
</head>

<body>
	<header>
		<?php
		echo sessionInformation();
		?>
	</header>

	<section>
		<h1> Utilisateur - <?php echo $school_name ?> </h1>
		<?php
		if(accountIsConnected()) {
			if(userIsConnected()) {
				echo displaySchool();
			} else {
				echo('Vous n\'êtes pas autorisé à accèder à cette page.');
			}
		} else {
			echo('Veuillez vous connecter pour accèder à cette page.');
			echo displayLogButton();
			echo logButton();
		}
		echo displayBackButton();
		echo backButton('user_search_school.php');
		?>
	</section>
</body>
</html>
