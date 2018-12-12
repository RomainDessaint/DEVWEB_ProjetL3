<?php

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

//fonction de connexion à la base de données
function db_connection() {
    require 'conf_db.inc.php';
    //création de la connexion
    $connection = new mysqli($host, $login, $password, $dbname, $port);
    //vérification de la connexion
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    return $connection;
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

//Fonction d'affichage d'un formulaire de redirection vers la page d'inscription
function notRegisteredForm() {
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td> Pas encore inscrit ? </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="submit" name="sign_in" value="S\'inscire"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//Fonction de redirection vers la page d'inscription
function notRegistered() {
    if(isset($_POST['sign_in'])) {
        header('location: account_sign_in_choice.php');
    }
}

//fonction d'affichage du formulaire de choix de type de compte à créer lors d'une inscription
function signInChoiceForm() {
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td> S\'inscire en tant que : </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="radio" name="account_choice" value="1"> Utilisateur </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="radio" name="account_choice" value="2"> Manager VA </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="submit" name="account_next" value="Suivant">';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//fonction de redirection en fonction du type de compte à créer lors d'une inscription
function signInChoice() {
    if(isset($_POST['account_next'])) {
        if(isset($_POST['account_choice'])) {
            $account_choice = $_POST['account_choice'];
            if($account_choice == 1)
            header('location: account_sign_in.php?account=1');
            if($account_choice == 2)
            header('location: account_sign_in.php?account=2');
        } else {
            return '<p style="color: red"> Veuillez séléctionner le type de compte à créer svp. </p>';
        }
    }
}

//Fonction d'affichage d'un formulaire d'un utilisateur ou d'une organisation
function signInForm() {
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $user_role = $_GET['account'];
    if($user_role == 1)
    $return .= '<td colspan=2> <h3> Créer un compte utilisateur : </h3>';
    else if($user_role == 2)
    $return .= '<td colspan=2> <h3> Créer un compte manager VA : </h3>';
    else if($user_role == 3)
    $return .= '<td colspan=2> <h3> Créer un compte administrateur CLM : </h3>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nom : </td> <td> <input type="text" name="user_name"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Prénom </td> <td> <input type="text" name="user_forename"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Identifiant </td> <td> <input type="text" name="user_log"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe </td> <td> <input type="password" name="user_pass1"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe </td> <td> <input type="password" name="user_pass2"> </td>';
    $return .= '</tr> <tr>';
    if($user_role != 3) {
        $connection = db_connection();
        $query = "SELECT school_id, school_name FROM schools ORDER BY school_name";
        $results = $connection->query($query);
        $return .= '<td> Sélectionnez votre école </td>';
        $return .= '<td> <select name="user_school">';
        $return .= '<option value="" disabled selected hidden> Choisissez une école </option>';
        while($row = $results->fetch_assoc()) {
            $return .= '<option value="' .$row['school_id'] .'">' .$row['school_name'] .'</option>';
        }
        $return .= '</select> </td>';
        $return .= '</tr> <tr>';
        $connection->close();
    }
    if($user_role == 2) {
        $return .= '<td colspan=2> Joindre un justificatif de votre rôle dans la vie associative de cette école : </td>';
        $return .= '</tr> <tr>';
        $return .= '<td colspan=2> <input id="real_button" hidden="hidden" type="file" name="org_report_upl"/>';
        $return .= '<button type="button" id="fake_button"> Choisir un fichier </button> <span id="fake_text"> Aucun fichier choisi. </span> </td>';
        $return .= '</tr> <tr>';
    }
    $return .= '<td colspan=2> <input type="submit" name="sign_in" value="S\'inscrire"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    $return .= '<script src="../includes/js/upload_btn.js"></script>';
    return $return;
}

//Fonction d'inscription d'un utilisateur ou d'une organisation
function signIn() {
    $connection = db_connection();
    $return = null;
    if(isset($_POST['sign_in'])) {
        $user_name = $_POST['user_name'];
        $user_forename = $_POST['user_forename'];
        $user_log = $_POST['user_log'];
        $user_pass1 = $_POST['user_pass1'];
        $user_pass2 = $_POST['user_pass2'];
        $user_role = $_GET['account'];
        if($user_role == 1)
        $user_validated = 1;
        else
        $user_validated = 0;
        if(isset($_POST['user_school'])) {
            $user_school = $_POST['user_school'];
            if($user_name != "" AND $user_forename !="" AND $user_log != "" AND $user_pass1 != "" AND $user_pass2 != "") {
                if($user_pass1 === $user_pass2) {
                    if(stringVerify($user_name) AND stringVerify($user_forename) AND stringVerify($user_log)) {
                        $query = "SELECT user_login FROM users WHERE user_login = '$user_log'";
                        $results = $connection->query($query);
                        if($results->num_rows == 0) {
                            $query = "INSERT INTO users(user_name, user_forename, user_login, user_pass, user_role, user_validated, user_school_id) VALUES ('$user_name', '$user_forename', '$user_log', '$user_pass1', '$user_role', '$user_validated', '$user_school')";
                            $results = $connection->query($query);
                            if ($connection->affected_rows == 1) {
                                if($user_role == 1) {
                                    $return = 'Inscription effectuée.';
                                    addStudent($user_school);
                                } else {
                                    $return = 'Demande d\'inscription effectuée.';
                                }
                            } else {
                                $return = 'Erreur lors de l\'inscription:' .$query .'<br>' .$connection->error;
                            }
                        } else {
                            $return = 'Cet identifiant est déja utilisé.';
                        }
                        $connection->close();
                    } else {
                        $return = 'Le nom, le prénom et l\'identifiants ne peuvent pas contenir de caractères spéciaux';
                    }
                } else {
                    $return = 'Les mots de passe ne correspondent pas.';
                }
            } else {
                $return = 'Veuillez remplir les champs requis.';
            }
        } else {
            $return = 'Veuillez remplir les champs requis.';
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

//Fonction qui permet d'incrémenter le nombre d'étudiant d'une école lors d'une incription
function addStudent($school_id) {
    $connection = db_connection();
    $query = "UPDATE schools SET school_nb_students = school_nb_students + 1 WHERE school_id = $school_id";
    $connection->query($query);
}

//fonction d'affichage du formulaire de connexion
function logInForm() {
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=2> Connectez-vous </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Identifiant : </td>';
    $return .= '<td> <input type="text" name="login" autocomplete="off"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe : </td>';
    $return .= '<td> <input type="password" name="password" autocomplete="off"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2> <input type="submit" value="Se connecter" name="log_in"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//fonction de vérification des identifiants/mots de passe dans la base de données
function logIn() {
    $connection = db_connection();
    $return = null;
    if(isset($_POST['log_in'])) {
        if($_POST['login'] != "" && $_POST['password'] != "") {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $query = "SELECT * FROM users WHERE user_login = '$login' AND user_pass = '$password' AND user_validated = 0";
            $result = $connection->query($query);
            if($result->num_rows == 0) {
                $query = "SELECT * FROM users WHERE user_login = '$login' AND user_pass = '$password' AND user_validated = 1";
                $results = $connection->query($query);
                if($results->num_rows > 0) {
                    while($row = $results->fetch_assoc()) {
                        $return = 'Identifiants corrects.';
                        session_start();
                        $_SESSION['account_connected'] = true;
                        $_SESSION['account_id'] = $row['user_id'];
                        $_SESSION['account_login'] = $row['user_login'];
                        $_SESSION['account_name'] = $row['user_name'];
                        $_SESSION['account_forename'] = $row['user_forename'];
                        $_SESSION['account_role'] = $row['user_role'];
                        echo($_SESSION['account_connected'] .' ' .$_SESSION['account_login'] .' ' .$_SESSION['account_name'] .' ' .$_SESSION['account_forename'] .' ' .$_SESSION['account_role']);
                        header('location: ../index.php');
                    }
                } else {
                    $return = 'Identifiants incorrects.';
                }
            } else {
                $return = 'Votre demande d\'inscription n\'a pas encore été validée.';
            }
            $connection->close();
        }
        else {
            if($_POST['login'] == "" && $_POST['password'] != "") {
                $return = 'Veuillez renseigner votre identifiant svp.';
            }
            else if($_POST['login'] != "" && $_POST['password'] == "") {
                $return = 'Veuillez renseigner votre mot de passe svp.';
            }
            else {
                $return = 'Veuillez renseigner votre identifiant et votre mot de passe svp.';
            }
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

//fonction d'affichage d'informations sur la session en cours
function sessionInformation($c_page='default') {
    $return = '<table>';
    $return .= '<tr>';
    if(!isset($_SESSION['account_connected'])) {
        $return .= '<td> Vous n\'êtes pas connecté. </td>';
        $return .= '<td> <form action="pages/account_log_in.php" method="POST"> <input type="submit" value="Se connecter" name="account_log_in"> </form> </td>';
    } else if(isset($_SESSION['account_connected']) && $_SESSION['account_connected'] == true) {
        $return .= '<td> Connecté en tant que : </td>';
        $return .= '<td>' .$_SESSION['account_forename'] .' ' .$_SESSION['account_name'] .'</td>';
        switch($_SESSION['account_role']) {
            case 1 :
            $account_role = 'Utilisateur';
            break;
            case 2 :
            $account_role = 'Manager VA';
            break;
            case 3:
            $account_role = 'Administrateur';
            break;
            default:
            $account_role = 'Utilisateur';
            break;
        }
        $return .= '<td> Profil : ' .$account_role .'</td>';
        if($c_page == 'index')
        $return .= '<td> <form action="pages/account_log_out.php" method="POST"> <input type="submit" value="Se déconnecter" name="account_log_out"> </form> </td>';
        else if($c_page == 'default')
        $return .= '<td> <form action="account_log_out.php" method="POST"> <input type="submit" value="Se déconnecter" name="account_log_out"> </form> </td>';
    }
    $return .= '</tr>';
    $return .= '</table>';
    return $return;
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

//Fonction d'affichage des caractéristiques de l'école de l'utilisateur connecté
function displayMySchool() {
    $connection = db_connection();
    $user_id = $_SESSION['account_id'];
    $query = "SELECT user_school_id FROM users where user_id = $user_id";
    $result = $connection->query($query);
    $row = $result->fetch_assoc();
    $user_school_id = $row['user_school_id'];
    $query = "SELECT * FROM schools where school_id = $user_school_id";
    $result = $connection->query($query);
    $row = $result->fetch_assoc();
    $return = '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=2> Mon école </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2>' .$row['school_name'] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Adresse : ' .$row['school_adress'] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nombre d\'utilisateurs : ' .$row['school_nb_students'] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nombre d\'associations : ' .$row['school_nb_organization'] .'</td>';
    $return .= '</tr>';
    $return .= '</table>';
    return $return;
}

//Fonction d'affichage d'un bouton pour changer d'école
function displayChangeSchoolButton() {
    $return = '<form action="#" method="POST">';
    $return .= '<input type="submit" name="change_school" value="Retour">';
    $return .= '</form>';
    return $return;
}

//Fonction d'affichage du formulaire de changement d'école
function changeSchoolForm() {
    $return = null;
    if(isset($_POST['change_school'])) {
        $connection = db_connection();
        $query = "SELECT school_id, school_name FROM schools ORDER BY school_name";
        $results = $connection->query($query);
        $return = '<form action="#" method="POST">';
        $return = '<td> Sélectionnez votre école </td>';
        $return .= '<td> <select name="user_school">';
        $return .= '<option value="" disabled selected hidden> Choisissez une école </option>';
        while($row = $results->fetch_assoc()) {
            $return .= '<option value="' .$row['school_id'] .'">' .$row['school_name'] .'</option>';
        }
        $return .= '</select> </td>';
        $return .= '</tr> <tr>';
        $connection->close();
    }
}

//Fonction d'affichage d'un formulaire de recherche d'école
function searchSchoolForm(){
	$return = '<form action="#" method="POST" style="margin-bottom:300px">';
	$return .= '<table>';
	$return .= '<tr>';
	$return .= '<td colspan=2> <h3> Trouver votre école : </h3> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> <input type="text" id="school_id" onkeyup="autocomplet()" name="searched_school" autocomplete="off"> <ul id="school_list_id"> </ul> </td> </td>';
	$return .= '<td> <input type="submit" name="search_school" value="Rechercher"> </td>';
	$return .= '</tr>';
	$return .= '</table>';
	$return .= '</form>';
    $return .= '<script type="text/javascript" src="../includes/js/jquery.min.js"></script>';
	$return .= '<script type="text/javascript" src="../includes/js/autocomplete.js"></script>';
	return $return;
}

//Fonction de recherche d'école
function searchSchool() {
    $return = null;
    if(isset($_POST['search_school']) && $_POST['searched_school'] != "") {
        $connection = db_connection();
        $searched_school = $_POST['searched_school'];
        $query = "SELECT * FROM schools WHERE school_name = '$searched_school'";
        $result = $connection->query($query);
        if($result->num_rows > 0){
            while ($row = $result->fetch_assoc()) {
                $school_id = $row['school_id'];
                header('Location: user_display_school.php?searched_school='.$school_id);
            }
        }	else {
            $return = "L'école recherchée n'existe pas.";
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

//Fonction de récupération du nom d'une école
function getSchoolName() {
    $connection = db_connection();
    if(isset($_GET['searched_school'])) {
        $school_id = $_GET['searched_school'];
        $query = "SELECT school_name FROM schools WHERE school_id = '$school_id'";
        $result = $connection->query($query);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $school_name = $row['school_name'];
            return $school_name;
        } else {
            return null;
        }
    } else {
        return null;
    }
}

function displaySchool() {
    if(isset($_GET['searched_school'])) {
        $connection = db_connection();
        $school_id = $_GET['searched_school'];
        $query = "SELECT * FROM schools where school_id = $school_id";
        $result = $connection->query($query);
        $row = $result->fetch_assoc();
        $return = '<table>';
        $return .= '<tr>';
        $return .= '<td colspan=2> Information école </td>';
        $return .= '</tr> <tr>';
        $return .= '<td colspan=2>' .$row['school_name'] .'</td>';
        $return .= '</tr> <tr>';
        $return .= '<td> Adresse : ' .$row['school_adress'] .'</td>';
        $return .= '</tr> <tr>';
        $return .= '<td> Nombre d\'utilisateurs : ' .$row['school_nb_students'] .'</td>';
        $return .= '</tr> <tr>';
        $return .= '<td> Nombre d\'associations : ' .$row['school_nb_organization'] .'</td>';
        $return .= '</tr>';
        $return .= '</table>';
    } else {
        $return = 'Aucune école à afficher.';
    }
    return $return;
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

function displayMenu() {
    $user_role = $_SESSION['account_role'];
    $return = null;
    switch($user_role) {
        case 1:
        $return .= '<ul>';
        $return .= '<li> <a href="user_display_my_school.php"> Voir mon école </a> </li>';
        $return .= '<li> <a href="user_search_school.php"> Chercher une école </a> </li>';
        $return .= '<li> <a href="#"> Adhérer à une association </a> </li>';
        $return .= '<li> <a href="#"> Je suis membre d\'une association </a> </li>';
        $return .= '</ul>';
        break;
        case 2:
        break;
        case 3:
        break;
    }
    return $return;
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

//Fonction de vérification qu'une session est ouverte
function accountIsConnected() {
    if(isset($_SESSION['account_connected']) && $_SESSION['account_connected'] == true) {
        return true;
    } else {
        return false;
    }
}

//Fonction de vérification qu'un utilisateur est connecté
function userIsConnected() {
    if(isset($_SESSION['account_role']) && $_SESSION['account_role'] == 1) {
        return true;
    } else {
        return false;
    }
}

//Fonction de vérification qu'un manager est connecté
function managerIsConnected() {
    if(isset($_SESSION['account_role']) && $_SESSION['account_role'] == 2) {
        return true;
    } else {
        return false;
    }
}

//Fonction de vérification qu'un administrateur est connecté
function adminIsConnected() {
    if(isset($_SESSION['account_role']) && $_SESSION['account_role'] == 3) {
        return true;
    } else {
        return false;
    }
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

//Fonction d'affichage du bouton de redirection au menu
function displayGoButton() {
    $return = '<form action="#" method="POST">';
    $return .= '<input type="submit" name="go" value="LET\'S GO !">';
    $return .= '</form>';
    return $return;
}

//Fonction de redirection au menu en fonction du type d'utilisateur connecté
function goButton() {
    if(isset($_POST['go'])) {
        if(accountIsConnected()) {
            switch($_SESSION['account_role']) {
                case 1:
                header('location: pages/user_logged.php');
                break;
                case 2:
                header('location: pages/manager_logged.php');
                break;
                case 3:
                header('location: pages/admin_logged.php');
                break;
            }
        } else {
            header('location: pages/account_log_in.php');
        }
    }
}

//Fonction d'affichage du bouton de redirection vers la page de connexion
function displayLogButton() {
    $return = '<form action="#" method="POST">';
    $return .= '<input type="submit" name="need_log_in" value="Se connecter">';
    $return .= '</form>';
    return $return;
}

//Fonction de redirection vers la page de connexion
function logButton() {
    if(isset($_POST['need_log_in']))
    header('location: account_log_in.php');
}

//Fonction d'affichage du bouton de retour
function displayBackButton() {
    $return = '<form action="#" method="POST">';
    $return .= '<input type="submit" name="back" value="Retour">';
    $return .= '</form>';
    return $return;
}

//Fonction de redirection vers la page précèdente
function backButton($location = '../index.php') {
    if(isset($_POST['back']))
    header('location: ' .$location);
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

//fonction de validation d'une chaîne de caractères
function stringVerify($string) {
	$not_allowed = array("\\", "/", ":", ";", ",", "*", "?", "\"", ">", "<", "|", ".");
	$count = count($not_allowed);

	for($i = 0; $i<$count; $i++){
		$pos = strpos($string, $not_allowed[$i]);
		if($pos === false) {
			$verified = true;
		} else {
			$verified = false;
			return $verified;
		}
	}
	return $verified;
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

?>
