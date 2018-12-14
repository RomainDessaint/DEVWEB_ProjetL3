<?php
include('../includes/functions.inc.php');
session_start();
$org_name = getOrgName();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title> Manager - <?php echo $org_name ?> </title>
</head>

<body>
	<header>
		<?php
		echo sessionInformation();
		?>
	</header>

	<section>
		<h1> Manager - <?php echo $org_name ?> </h1>
		<?php
		if(accountIsConnected()) {
			if(managerIsConnected()) {
				echo displayOrg();
			} else {
				echo('Vous n\'êtes pas autorisé à accèder à cette page.');
			}
		} else {
			echo('Veuillez vous connecter pour accèder à cette page.');
			echo displayLogButton();
			echo logButton();
		}
		echo displayBackButton();
		echo backButton('user_display_school.php');
		?>
	</section>
</body>
</html>
