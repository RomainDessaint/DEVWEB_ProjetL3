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
		if(adminIsConnected()) {
			echo registerManagerForm();
            echo registerManager();
		} else {
			header('location: account_log_in.php');
		}
		echo displayBackButton();
		echo backButton('../index.php');
		?>
	</section>
</body>
</html>
