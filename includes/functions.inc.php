<?php

/*------------------------------------------------BASE DE DONNEES------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/

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

/*--------------------------------------------------UTILISATEUR--------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------INSCRIPTION--------------------------------------------------*/

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
        header('location: account_sign_in.php');
    }
}

//Fonction d'affichage d'un formulaire d'inscription
function signInForm() {
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=2> <h3> Créer un compte utilisateur : </h3>';
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
    $return .= '<td colspan=2> <input type="submit" name="sign_in" value="S\'inscrire"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    $return .= '<script src="../includes/js/upload_btn.js"></script>';
    return $return;
}

//Fonction d'inscription d'un utilisateur
function signIn() {
    $connection = db_connection();
    $return = null;
    if(isset($_POST['sign_in'])) {
        $user_name = $_POST['user_name'];
        $user_forename = $_POST['user_forename'];
        $user_log = $_POST['user_log'];
        $user_pass1 = $_POST['user_pass1'];
        $user_pass2 = $_POST['user_pass2'];
        if(isset($_POST['user_school'])) {
            $user_school = $_POST['user_school'];
            if($user_name != "" AND $user_forename !="" AND $user_log != "" AND $user_pass1 != "" AND $user_pass2 != "") {
                if($user_pass1 === $user_pass2) {
                    if(stringVerify($user_name) AND stringVerify($user_forename) AND stringVerify($user_log)) {
                        $query = "SELECT user_login FROM users WHERE user_login = '$user_log'";
                        $results = $connection->query($query);
                        if($results->num_rows == 0) {
                            $query = "INSERT INTO users(user_name, user_forename, user_login, user_pass, user_role, user_school_id) VALUES ('$user_name', '$user_forename', '$user_log', '$user_pass1', 1, '$user_school')";
                            $results = $connection->query($query);
                            if($connection->affected_rows == 1) {
                                $return = 'Inscription effectuée.';
                                addStudent($user_school);
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

//Fonction qui permet de décrémenter le nombre d'étudiant d'une école lors d'une incription
function removeStudent($school_id) {
    $connection = db_connection();
    $query = "UPDATE schools SET school_nb_students = school_nb_students - 1 WHERE school_id = $school_id";
    $connection->query($query);
}

/*---------------------------------------------------CONNEXION---------------------------------------------------*/

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
            $query = "SELECT * FROM users WHERE user_login = '$login' AND user_pass = '$password'";
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
                    if($row['user_role'] == 1 || $row['user_role'] == 2)
                    $_SESSION['account_school_id'] = $row['user_school_id'];
                    header('location: ../index.php');
                }
            } else {
                $return = 'Identifiants incorrects.';
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

/*----------------------------------------------AFFICHER UNE ECOLE-----------------------------------------------*/

//Fonction d'affichage des informations de l'école de l'utilisateur connecté
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

//Fonction d'affichage des informations d'une école
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

/*------------------------------------------------CHANGER D'ECOLE------------------------------------------------*/

//Fonction d'affichage d'un bouton pour changer d'école
function displayChangeSchoolButton() {
    $return = '<form action="#" method="POST">';
    $return .= '<input type="submit" name="change_school" value="Changer d\'école">';
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
        $return .= '<table>';
        $return .= '<tr>';
        $return .= '<td colspan=2> Sélectionnez votre nouvelle école </td>';
        $return .= '</tr> <tr>';
        $return .= '<td> <select name="new_school">';
        $return .= '<option value="" disabled selected hidden> Choisissez une école </option>';
        while($row = $results->fetch_assoc()) {
            $return .= '<option value="' .$row['school_id'] .'">' .$row['school_name'] .'</option>';
        }
        $return .= '</select> </td>';
        $return .= '<td> <input type="submit" name="set_new_school" value="Valider"> </td>';
        $return .= '</tr>';
        $return .= '</table>';
        $return .= '</form>';
        $connection->close();
    }
    return $return;
}

function changeSchool() {
    $return = null;
    if(isset($_POST['set_new_school'])) {
        if(isset($_POST['new_school'])) {
            $user_id = $_SESSION['account_id'];
            $currrent_school = $_SESSION['account_school_id'];
            $new_school = $_POST['new_school'];
            $connection = db_connection();
            $query = "UPDATE users SET user_school_id = $new_school WHERE user_id = $user_id";
            removeStudent($currrent_school);
            addStudent($new_school);
            $_SESSION['account_school_id'] = $new_school;
            $connection->query($query);
            $connection->close();
            header('refresh: 0');
        } else {
            $return = 'Veuillez séléctionner votre nouvelle école svp.';
        }
    }
    $return = '<p styl="color: red;">' .$return .'</p>';
    return $return;
}

/*----------------------------------------------RECHERCHER UNE ECOLE---------------------------------------------*/

//Fonction d'affichage d'un formulaire de recherche d'école
function searchSchoolForm() {
    $return = '<form action="#" method="POST" style="margin-bottom:300px">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=2> <h3> Chercher une école : </h3> </td>';
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
                if($_SESSION['account_role'] == 1)
                header('Location: user_display_school.php?searched_school='.$school_id);
                if($_SESSION['account_role'] == 2)
                header('Location: manager_display_school.php?searched_school='.$school_id);
                if($_SESSION['account_role'] == 3)
                header('Location: admin_display_school.php?searched_school='.$school_id);
            }
        }	else {
            $return = "L'école recherchée n'existe pas.";
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

/*---------------------------------------------CREER UNE ASSOCIATION---------------------------------------------*/

//Fonction d'affichage du formulaire de création d'associations
function createOrgForm() {
    $connection = db_connection();
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=2> <h3> Créer mon association </h3> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nom de l\'assocation : </td> <td> <input type="text" name="org_name" autocomplete="off"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mon poste dans l\'assocation : </td> <td> <input type="text" name="member_role" autocomplete="off"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Description de l\'assocation : </td> <td> <textarea name="org_description"></textarea> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Je certifie être responsable de l\'association : </td>';
    $return .= '<td> <input type="checkbox" value="is_responsable" name="org_responsable"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> J\'accepte les conditions de la charte associative : </td>';
    $return .= '<td> <input type="checkbox" value="rules_accepted" name="org_rules"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2> <input type="submit" name="org_create"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    $connection->close();
    return $return;
}

//Fonction de création d'associations
function createOrg() {
    $connection = db_connection();
    $return = null;
    if(isset($_POST['org_create'])) {
        $org_school_id = $_SESSION['account_school_id'];
        $org_name = $_POST['org_name'];
        $member_role = $_POST['member_role'];
        $org_description = $_POST['org_description'];
        if($org_name != "" AND $org_description != "" AND $member_role != "") {
            if(isset($_POST['org_responsable']) AND isset($_POST['org_rules'])) {
                $org_reponsable = $_POST['org_responsable'];
                $org_rules = $_POST['org_rules'];
                if($org_reponsable == "is_responsable") {
                    if($org_rules == "rules_accepted") {
                        $org_name = $_POST['org_name'];
                        $query = "SELECT organization_name FROM organizations WHERE organization_name = '$org_name'";
                        $result = $connection->query($query);
                        if($result->num_rows == 0) {
                            if(strlen($org_name) < 50) {
                                if(strlen($org_description) < 500) {
                                    if(stringVerify($org_name)) {
                                        if(stringVerify($org_description)) {
                                            if(stringVerify($member_role)) {
                                                $query = "INSERT INTO organizations(organization_name, organization_description, organization_school_id, organization_nb_members, organization_validated) VALUES ('$org_name', '$org_description', '$org_school_id', 0, 0)";
                                                $results = $connection->query($query);
                                                if ($connection->affected_rows == 1) {
                                                    $return = 'Demande d\'inscription de l\'association effectuée.';
                                                    $query = "SELECT organization_id FROM organizations WHERE organization_name = '$org_name'";
                                                    $result = $connection->query($query);
                                                    $row = $result->fetch_assoc();
                                                    $org_id = $row['organization_id'];
                                                    $user_id = $_SESSION['account_id'];
                                                    addMember($org_id, $user_id, $member_role);
                                                } else {
                                                    $return = 'Un problème est survenu lors de l\'inscription de l\'association.';
                                                }
                                            } else {
                                                $return = 'Le nom du poste ne peut pas contenir de cractères spéciaux.';
                                            }
                                        } else {
                                            $return = 'La description de l\'association ne peut pas contenir de cractères spéciaux.';
                                        }
                                    } else {
                                        $return = 'Le nom de l\'association ne peut pas contenir de cractères spéciaux.';
                                    }
                                } else {
                                    $return = 'La description de l\'association ne peut pas dépasser les 500 caractères';
                                }
                            } else {
                                $return = 'Le nom de l\'assocation ne peut pas dépasser les 50 caractères.';
                            }
                        } else {
                            $return = 'Ce nom d\'association est déja utilisé.';
                        }
                        $connection->close();
                    } else {
                        $return = 'Vous devez accepter la charte associative.';
                    }
                } else {
                    $return = 'Vous devez être responsable de l\'association pour pouvoir l\'inscrire';
                }
            } else {
                $return = 'Veuillez remplir les champs requis.';
            }
        } else {
            $return = 'Veuillez remplir les champs requis.';
        }
    }
    $return = '<p style="color: red">' .$return .'</p>';
    return $return;
}

/*-------------------------------------------REJOINDRE UNE ASSOCIATION-------------------------------------------*/

//Fonction d'affichage d'un formulaire pour rejoindre une association
function joinOrgForm() {
    $return = null;
    $connection = db_connection();
    $query = "SELECT organization_id, organization_name FROM organizations WHERE organization_validated = 1 ORDER BY organization_name";
    $results = $connection->query($query);
    if($results->num_rows != 0) {
        $return = '<form action="#" method="POST">';
        $return .= '<table>';
        $return .= '<tr>';
        $return .= '<td> Sélectionnez votre association </td>';
        $return .= '<td> <select name="org_joined">';
        $return .= '<option value="" disabled selected hidden> Choisissez une association </option>';
        while($row = $results->fetch_assoc()) {
            $return .= '<option value="' .$row['organization_id'] .'">' .$row['organization_name'] .'</option>';
        }
        $return .= '</select> </td>';
        $return .= '</tr> <tr>';
        $return .= '<td> Mon poste dans l\'assocation : </td> <td> <input type="text" name="member_role"> </td>';
        $return .= '</tr> <tr>';
        $return .= '<td> <input type="submit" name="join_org" value="Rejoindre l\'association"> </td>';
        $return .= '</tr>';
        $return .= '</table>';
        $return .= '</form>';
    } else {
        $return = 'Aucune assocation renseignée pour le moment.';
    }
    $connection->close();
    return $return;
}

//Fonction d'ajout d'un membre dans une assocation
function joinOrg() {
    $return = null;
    $connection = db_connection();
    if(isset($_POST['join_org'])) {
        if(isset($_POST['org_joined']) AND $_POST['org_joined'] != "") {
            $org_id = $_POST['org_joined'];
            $user_id = $_SESSION['account_id'];
            $member_role = $_POST['member_role'];
            if($member_role != "") {
                if(stringVerify($member_role)) {
                    $query = "SELECT member_organization_id, member_user_id, member_validated FROM members WHERE member_user_id = '$user_id'";
                    $result = $connection->query($query);
                    if($result->num_rows == 0) {
                        addMember($org_id, $user_id, $member_role);
                        $query = "SELECT organization_name FROM organizations WHERE organization_id = '$org_id'";
                        $result = $connection->query($query);
                        $row = $result->fetch_assoc();
                        $org_name = $row['organization_name'];
                        $return = 'Votre demande pour rejoindre ' .$org_name .' a été prise en compte.';
                    } else {
                        while($row = $result->fetch_assoc()) {
                            if($row['member_organization_id'] != $org_id) {
                                addMember($org_id, $user_id, $member_role);
                                $query = "SELECT organization_name FROM organizations WHERE organization_id = '$org_id'";
                                $result = $connection->query($query);
                                $row = $result->fetch_assoc();
                                $org_name = $row['organization_name'];
                                $return = 'Votre demande pour rejoindre ' .$org_name .' a été prise en compte.';
                            } else {
                                if($row['member_validated'] == 1)
                                $return = 'Vous faites déja partie de cette association.';
                                else
                                $return = 'Vous avez déja fait une demande pour rejoindre cette association.';
                            }
                        }
                    }
                } else {
                    $return = 'Le nom du poste ne peut pas contenir de cractères spéciaux.';
                }
            } else {
                $return = 'Veuillez renseigner votre poste dans l\'association.';
            }
        } else {
            $return = 'Veuillez séléctionner une association.';
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

//Fonction de création d'un membre
function addMember($org_id, $user_id, $member_role) {
    $query = "INSERT INTO members VALUES('$member_role', '$org_id', '$user_id', 0)";
    $connection = db_connection();
    $connection->query($query);
    $connection->close();
}

/*--------------------------------------------AFFICHER LES ASSOCIATION-------------------------------------------*/

//Fonction d'affichage de la liste des association d'une école
function orgList() {
    $return = null;
    $connection = db_connection();
    if(isset($_GET['searched_school'])) {
        $school_id = $_GET['searched_school'];
    } else {
        $school_id = $_SESSION['account_school_id'];
    }
    $query = "SELECT * FROM organizations WHERE organization_school_id = '$school_id'";
    $results = $connection->query($query);
    if($results->num_rows > 0) {
        $return .= '<form action="#" method="POST">';
        $return .= '<table>';
        $return .= '<td colspan=3> Liste des associations </td>';
        $return .= '<tr> <th> Nom de l\'association </th> <th> Description </th> <th> En savoir plus </th> </tr>';
        while($row = $results->fetch_assoc()) {
            $return .= '<tr>';
            $return .= '<td>' .$row['organization_name'] .'</td>';
            $return .= '<td>' .$row['organization_description'] .'</td>';
            $return .= '<td> <input type="submit" name="more_information_'.$row['organization_id'] .'" value="En savoir plus"> </td>';
            $return .= '</tr>';
        }
        $return .= '</table>';
        $return .= '</form>';
    } else {
        $return = 'Aucune association enregistrée dans cette école.';
    }
    return $return;
}

//Fonction de redirection vers la page d'une association
function moreInformationOrg() {
    $return = null;
    $connection = db_connection();
    if(isset($_GET['searched_school'])) {
        $school_id = $_GET['searched_school'];
    } else {
        $school_id = $_SESSION['account_school_id'];
    }
    $query = "SELECT * FROM organizations WHERE organization_school_id = '$school_id' AND organization_validated = 1";
    $results = $connection->query($query);
    for ($i=0; $i < $results->num_rows; $i++) {
        while($row = $results->fetch_assoc()){
            if(isset($_POST['more_information_'.$row['organization_id']])) {
                $org_id = $row['organization_id'];
                $account_role = $_SESSION['account_role'];
                if($account_role == 1)
                header('location: user_display_org.php?searched_org='.$org_id);
                if($account_role == 2)
                header('location: manager_display_org.php?searched_org='.$org_id);
                if($account_role == 3)
                header('location: admin_display_org.php?searched_org='.$org_id);
            }
        }
    }
}

//Fonction d'affichage d'une association
function displayOrg() {
    $return = null;
    $connection = db_connection();
    if(isset($_GET['searched_org'])) {
        $org_id = $_GET['searched_org'];
        $query = "SELECT * FROM members INNER JOIN organizations ON members.member_organization_id = '$org_id' AND organizations.organization_id = '$org_id' INNER JOIN users ON users.user_id = members.member_user_id";
        $result = $connection->query($query);
        $return = '<table>';
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $return .= '<tr>';
            $return .= '<td> <h2> '.$row['organization_name'].'</h2> </td>';
            $return .= '</tr> <tr>';
            $return .= '<td> Membres dans l\'association : '.$row['organization_nb_members'].'</td>';
            $return .= '</tr> </table>';

            $return .= '<table>';
            $return .= '<tr>';
            $return .= '<td colspan=3> <h3> Membres de l\'association </h3> </td>';
            $return .= '</tr> <tr>';
            $return .= '<th> Prénom </th> <th> Nom </th> <th> Rôle </th>';
            $return .= '</tr>';
            $return .= '<tr>';
            $return .= '<td>'.$row['user_forename'].'</td>';
            $return .= '<td>'.$row['user_name'].'</td>';
            $return .= '<td>'.$row['member_role'].'</td>';
            $return .= '</tr>';
            while($row = $result->fetch_assoc()) {
                $return .= '<tr>';
                $return .= '<td>'.$row['user_forename'].'</td>';
                $return .= '<td>'.$row['user_name'].'</td>';
                $return .= '<td>'.$row['member_role'].'</td>';
                $return .= '</tr>';
            }
        } else {
            $query = "SELECT * FROM organizations WHERE organization_id = '$org_id'";
            $result = $connection->query($query);
            $row = $result->fetch_assoc();
            $return .= '<td> <h2> '.$row['organization_name'].'</h2> </td>';
            $return .= '</tr> <tr>';
            $return .= '<td> Membres dans l\'association : '.$row['organization_nb_members'].'</td>';
            $return .= '</tr> </table>';

            $return .= '<table>';
            $return .= '<tr>';
            $return .= '<td colspan=3> <h3> Membres de l\'association </h3> </td>';
            $return .= '</tr> <tr>';
            $return .= '<th> Prénom </th> <th> Nom </th> <th> Rôle </th>';
            $return .= '</tr> <tr>';
            $return .= '<td colspan=3> Aucun membres renseignés </td>';
            $return .= '</tr>';
        }
        $return .= '</table>';

        $user_id = $_SESSION['account_id'];
        $query = "SELECT member_user_id FROM members WHERE member_organization_id = '$org_id'";
        $result = $connection->query($query);
        while($row = $result->fetch_assoc()) {
            if($row['member_user_id'] == $user_id) {
                $return .= '<form method="POST">';
                $return .= '<input type="submit" name="manage_org" value="Gérer l\'association">';
                $return .= '</form>';
            }
        }
        $account_role = $_SESSION['account_role'];
        if($account_role == 2) {
            $account_school_id = $_SESSION['account_school_id'];
            $query = "SELECT organization_school_id FROM organizations WHERE organization_id = '$org_id'";
            $result = $connection->query($query);
            $row = $result->fetch_assoc();
            if($account_school_id == $row['organization_school_id']) {
                $return .= '<form method="POST">';
                $return .= '<input type="submit" name="manage_org" value="Gérer l\'association">';
                $return .= '</form>';
            }
        }
    }
    echo redirectManageOrg($org_id);
    return $return;
}

//Fonction de redirection vers la page de modification de l'association
function redirectManageOrg($org_id) {
    if(isset($_POST['manage_org'])) {
        $account_role = $_SESSION['account_role'];
        if($account_role == 1)
        header('location: user_manage_org.php?searched_org='.$org_id);
        if($account_role == 2)
        header('location: manager_manage_org.php?searched_org='.$org_id);
        if($account_role == 3)
        header('location: admin_manage_org.php?searched_org='.$org_id);
    }
}

/*--------------------------------------------MODIFIER LES ASSOCIATION-------------------------------------------*/

//Fonction de modification d'une association
function manageOrg() {
    $return = null;
    $connection = db_connection();
    if(isset($_GET['searched_org'])) {
        $org_id = $_GET['searched_org'];
        $query = "SELECT * FROM members INNER JOIN organizations ON members.member_organization_id = '$org_id' AND organizations.organization_id = '$org_id' INNER JOIN users ON users.user_id = members.member_user_id";
        $result = $connection->query($query);
        $return = '<form action=';
        if(isset($_POST['addMember'])) {
            $return .= '"#addMember" method="POST">';
        } else {
            $return .= '"#tableau" method="POST">';
        }
        $return .= '<table">';
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $return .= '<tr>';
            $return .= '<td> <h2> '.$row['organization_name'].'</h2> </td>';
            $return .= '</tr> <tr>';
            $return .= '<td> Membres dans l\'association : '.$row['organization_nb_members'].'</td>';
            $return .= '</tr> </table>';

            $return .= '<table id="tableau">';
            $return .= '<tr>';
            $return .= '<td colspan=3> <h3> Membres de l\'association </h3> </td>';
            $return .= '</tr> <tr>';
            $return .= '<th> Prénom </th> <th> Nom </th> <th> Rôle </th>';
            $return .= '</tr>';
            $return .= '<tr>';
            $return .= '<td>'.$row['user_forename'].'</td>';
            $return .= '<td>'.$row['user_name'].'</td>';
            $return .= '<td>'.$row['member_role'].'</td>';
            $return .= '<td> <input type="submit" name="modify_'.$row['member_user_id'].$row['member_organization_id'].'" value="Modifier"> </td>';
            $return .= '<td> <input type="submit" name="delete_'.$row['member_user_id'].$row['member_organization_id'].'" value="Supprimer"> </td>';
            $return .= '</tr>';
            while($row = $result->fetch_assoc()) {
                $return .= '<tr>';
                $return .= '<td>'.$row['user_forename'].'</td>';
                $return .= '<td>'.$row['user_name'].'</td>';
                $return .= '<td>'.$row['member_role'].'</td>';
                $return .= '<td> <input type="submit" name="modify_'.$row['member_user_id'].$row['member_organization_id'].'" value="Modifier"> </td>';
                $return .= '<td> <input type="submit" name="delete_'.$row['member_user_id'].$row['member_organization_id'].'" value="Supprimer"> </td>';
                $return .= '</tr>';
            }
        } else {
            $query = "SELECT * FROM organizations WHERE organization_id = '$org_id'";
            $result = $connection->query($query);
            $row = $result->fetch_assoc();
            $return .= '<td> <h2> '.$row['organization_name'].'</h2> </td>';
            $return .= '</tr> <tr>';
            $return .= '<td> Membres dans l\'association : '.$row['oragnization_nb_members'].'</td>';
            $return .= '</tr> </table>';

            $return .= '<table>';
            $return .= '<tr>';
            $return .= '<td colspan=3> <h3> Membres de l\'association </h3> </td>';
            $return .= '</tr> <tr>';
            $return .= '<th> Prénom </th> <th> Nom </th> <td> Rôle </th>';
            $return .= '</tr> <tr>';
            $return .= '<td colspan=3> Aucun membres renseignés </td>';
            $return .= '</tr>';
        }
        $return .= '<tr>';
        $return .= '<td colspan=5> <input type="submit" name="add_member" value="Ajouter des membres"> </td>';
        $return .= '</tr>';
        $return .= '</table>';
        $return .= '</form>';
    }
    return $return;
}

//Fonction d'affichage d'un formulaire d'ajout de membre à une association si on appuie sur le bouton "ajouter un membre"
function addMemberOrgFormIfSet() {
    if(isset($_POST['add_member'])) {
        echo addMemberOrgForm();
    }
}

//Fonction d'affichage d'un formulaire d'ajout de membre à une association
function addMemberOrgForm($forename = "", $name = "", $role = "", $btn = "validate_add_member", $value = "Ajouter") {
    $return = '<form action="#tableau" method="POST">';
    $return .= '<table id="addMember">';
    $return .= '<tr style="border-bottom:1px solid black;">';
    $return .= '<td> Prénom </td> <td> Nom </td> <td> Rôle </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="text" name="forename" value="'.$forename.'"> </td>';
    $return .= '<td> <input type="text" name="name" value="'.$name.'"> </td>';
    $return .= '<td> <input type="text" name="role" value="'.$role.'"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=3> <input type="submit" name="'.$btn.'" value="'.$value.'"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//Fonction d'ajout d'un membre dans une association par un membre de l'association
function addMemberOrg() {
    $connection = db_connection();
    $return = null;
    if(isset($_POST['validate_add_member'])) {
        if(isset($_GET['searched_org'])) {
            $org_id = $_GET['searched_org'];
            $member_name = $_POST['name'];
            $member_forename = $_POST['forename'];
            $member_role = $_POST['role'];
            $query = "SELECT * FROM users WHERE user_name = '$member_name' AND user_forename = '$member_forename'";
            $result = $connection->query($query);
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user_id = $row['user_id'];
                $query = "INSERT INTO members(member_role, member_user_id, member_organization_id, member_validated) VALUES('$member_role', '$user_id', '$org_id', 1)";
                $result = $connection->query($query);
                if($result == TRUE) {
                    $query = "UPDATE organizations SET organization_nb_members = organization_nb_members + 1 WHERE organization_id = '$org_id'";
                    $connection->query($query);
                    header('refresh: 0');
                } else {
                    $return = "Un prolème est survenu lors de l'ajout du membre.";
                }
                $connection->close();
            } else {
                $return = 'Cet utilisateur n\'existe pas.';
            }
        }
    }
    $return = '<p style="color: red;">'.$return .'</p>';
    return $return;
}

//FOnction d'affichage d'un formulaire de modification d'un membre d'une association
function orgManagementModifyForm() {
    $return = null;
    $connection = db_connection();
    if(isset($_GET['searched_org'])) {
        $org_id = $_GET['searched_org'];
        $query = "SELECT * FROM members INNER JOIN users ON members.member_user_id = users.user_id WHERE member_organization_id = '$org_id'";
        $result = $connection->query($query);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if(isset($_POST["modify_".$row['member_user_id'].$row['member_organization_id']])) {
                    $forename = $row['user_forename'];
                    $name = $row['user_name'];
                    $role = $row['member_role'];
                    $return = addMemberOrgForm($forename, $name, $role, "modify_member", "Modifier");
                }
            }
        }
    }
    return $return;
}

//Fonction de modification d'un membre d'une association
function orgManagementModify() {
    $return = null;
    $connection = db_connection();
    if(isset($_GET['searched_org'])) {
        if(isset($_POST['modify_member'])) {
            $org_id = $_GET['searched_org'];
            $member_forename = $_POST['forename'];
            $member_name = $_POST['name'];
            $member_role = $_POST['role'];
            $query = "SELECT * FROM users WHERE user_name = '$member_name' AND user_forename = '$member_forename'";
            $result = $connection->query($query);
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user_id = $row['user_id'];
                $query = "UPDATE members SET member_role = '$member_role', member_organization_id = '$org_id', member_user_id = '$user_id' WHERE member_user_id = '$user_id' AND member_organization_id = '$org_id'";
                $result = $connection->query($query);
                if($result === TRUE) {
                    header('refresh: 0');
                } else {
                    $return = "Un problème est survenu lors de la modification du membre.";
                }
            } else {
                $return = 'Cet utilisateur n\'existe pas.';
            }
        }
    }
    $return = '<p style="color:red;">' .$return .'</p>';
    return $return;
}

//Fonction de suppression d'un membre d'une association
function orgManagementDelete() {
    $connection = db_connection();
    $return = null;
    if(isset($_GET['searched_org'])) {
        $org_id = $_GET['searched_org'];
        $query = "SELECT * FROM members WHERE member_organization_id = '$org_id'";
        $result = $connection->query($query);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if(isset($_POST["delete_".$row['member_user_id'].$row['member_organization_id']])) {
                    $member_user_id = $row['member_user_id'];
                    $member_organization_id = $row['member_organization_id'];
                    $query = "DELETE FROM members WHERE member_user_id = '$member_user_id' AND member_organization_id = '$member_organization_id'";
                    $result = $connection->query($query);
                    if($result == TRUE) {
                        $query = "UPDATE organizations SET organization_nb_members = organization_nb_members - 1 WHERE organization_id = '$org_id'";
                        $connection->query($query);
                        $account_role = $_SESSION['account_role'];
                        if($account_role == 1)
                        header("refresh: 0;url=user_manage_org.php?searched_org=".$org_id."#tableau");
                        if($account_role == 2)
                        header("refresh: 0;url=manager_manage_org.php?searched_org=".$org_id."#tableau");
                        if($account_role == 3)
                        header("refresh: 0;url=admin_manage_org.php?searched_org=".$org_id."#tableau");
                        exit();
                    } else {
                        $return = "Un prolème est survenu lors de la suppression du membre.";
                    }
                }
            }
            $return = '<p style="color:red;">' .$return .'</p>';
            return $return;
        }
    }
}

/*-----------------------------------------------------DIVERS----------------------------------------------------*/

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


//Fonction de récupération du nom d'une association
function getOrgName() {
    $connection = db_connection();
    if(isset($_GET['searched_org'])) {
        $org_id = $_GET['searched_org'];
        $query = "SELECT organization_name FROM organizations WHERE organization_id = '$org_id'";
        $result = $connection->query($query);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $org_name = $row['organization_name'];
            return $org_name;
        } else {
            return null;
        }
    } else {
        return null;
    }
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

function memberChoiceForm() {
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td> <input type="submit" name="join_org_choice" value="Rejoindre une association"> </td>';
    $return .= '<td> <input type="submit" name="create_org_choice" value="Inscrire mon asscoiation"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

function memberChoice() {
    if(isset($_POST['join_org_choice'])) {
        header('location: user_join_org.php');
    }
    if(isset($_POST['create_org_choice'])) {
        header('location: user_create_org.php');
    }
}

/*----------------------------------------------------MANAGER----------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/

/*-------------------------------------------VALIDATION DES ASSOCIATION------------------------------------------*/

//Fonction d'affichage des associations en attente de validation
function awaitingValidationOrganizations() {
    $connection = db_connection();
    $school_id = $_SESSION['account_school_id'];
    $query = "SELECT school_name, organization_name, organization_id, user_name, user_forename FROM organizations INNER JOIN schools ON organizations.organization_school_id = schools.school_id INNER JOIN members ON organizations.organization_id = members.member_organization_id INNER JOIN users ON members.member_user_id = users.user_id WHERE organization_validated = 0 AND organization_school_id = '$school_id'";
    $result = $connection->query($query);
    if($result->num_rows > 0){
        $return = '<form action="#" method="POST">';
        $return .= '<table>';
        $return .= '<tr> <td colspan = 4> Associations à valider </td> </tr>';
        $return .= '<tr> <th> Association </th> <th> Ecole </th> <th> Demande </th> <th> Valider </th> </tr> ';
        while($row = $result->fetch_assoc()) {
            $return .= '<tr>';
            $return .= '<td>' . $row['organization_name'] . '</td>';
            $return .= '<td>' . $row['school_name'] . '</td>';
            $return .= '<td> Demande par '.$row['user_name'].' '.$row['user_forename'] .'</td>';
            $return .= '<td> <input type="submit" value="Valider la demande" name="validate_organization'.$row['organization_id'].'"> </td>';
            $return .= '</tr>';
        }
        $return .= '</table>';
        $return .= '</form>';
    } else {
        $return = "Il n'y a aucune association à valider.";
    }
    $return = '<p style="color:red">' .$return .'</p>';
    return $return;
}

//Fonction de validation des associations
function validatingOrganizations() {
    $connection = db_connection();
    $return = null;
    $school_id = $_SESSION['account_school_id'];
    $query = "SELECT organization_name, organization_id, organization_school_id FROM organizations INNER JOIN schools ON organizations.organization_school_id = schools.school_id INNER JOIN members ON organizations.organization_id = members.member_organization_id INNER JOIN users ON members.member_user_id = users.user_id WHERE organization_validated = 0 AND organization_school_id = '$school_id'";
    $result = $connection->query($query);
    for ($i=0; $i<$result->num_rows; $i++) {
        while($row = $result->fetch_assoc()) {
            if(isset($_POST['validate_organization'.$row['organization_id']])) {
                $org_id = $row['organization_id'];
                $org_school_id = $row['organization_school_id'];
                $query = "UPDATE organizations SET organization_validated = 1 WHERE organization_id = '$org_id'";
                $results = $connection->query($query);
                if($results === TRUE) {
                    $return = "La demande pour l'association ".$row['organization_name'] ." a été acceptée.";
                    $query = "UPDATE schools SET school_nb_organization = school_nb_organization + 1 WHERE school_id = '$school_id'";
                    $connection->query($query);
                    $query = "UPDATE SET school_nb_organization = school_nb_organization + 1 WHERE school_id = '$school_id'";
                    $connection->query($query);
                } else {
                    $return = "Un problème est survenu lors de la validation.";
                }
                header('refresh: 0');
                exit();
            }
        }
    }
    $return = '<p style="color:red;">' .$return .'</p>';
    return $return;
}

/*---------------------------------------------VALIDATION DES MEMBRES--------------------------------------------*/

//Fonction d'affichage des membres en attente de validation
function awaitingValidationMembers() {
    $connection = db_connection();
    $query = 'SELECT user_forename, user_name, organization_name, member_user_id FROM members INNER JOIN users ON members.member_user_id = users.user_id INNER JOIN organizations ON members.member_organization_id=organizations.organization_id WHERE member_validated = 0';
    $result = $connection->query($query);
    if($result->num_rows > 0){
        $return = '<form action="#" method="POST">';
        $return .= '<table>';
        $return .= '<tr> <td colspan = 4> Associations à valider </td> </tr>';
        $return .= '<tr> <th> Prénom </th> <th> Nom </th> <th> Association </th> <th> Valider </th> </tr> ';
        while($row = $result->fetch_assoc()) {
            $return .= '<tr>';
            $return .= '<td>' . $row['user_forename'] . '</td>';
            $return .= '<td>' . $row['user_name'] . '</td>';
            $return .= '<td>' . $row['organization_name'] .'</td>';
            $return .= '<td> <input type="submit" value="Valider la demande" name="validate_member'.$row['member_user_id'].'"> </td>';
            $return .= '</tr>';
        }
        $return .= '</table>';
    } else {
        $return = "Il n'y a aucun membre à valider.";
    }
    $return = '<p style="color:red;">' .$return .'</p>';
    return $return;
}

//Fonction de validation des membres des associations
function validatingMembers() {
    $return = null;
    $connection = db_connection();
    $query = 'SELECT member_user_id, user_name, user_forename, organization_id FROM members INNER JOIN users ON members.member_user_id = users.user_id INNER JOIN organizations ON members.member_organization_id=organizations.organization_id WHERE member_validated = 0';
    $result = $connection->query($query);
    for ($i=0; $i < $result->num_rows; $i++) {
        while($row = $result->fetch_assoc()) {
            if(isset($_POST['validate_member'.$row['member_user_id']])) {
                $org_id = $row['organization_id'];
                $query = 'UPDATE members SET member_validated = 1 WHERE member_user_id='.$row['member_user_id'];
                $result = $connection->query($query);
                if ($result === TRUE) {
                    $return = "La demande de " .$row['user_forename'] ." " .$row['user_name'] ."a bien été acceptée <hr/>";
                    $query = "UPDATE organizations SET organization_nb_members = organization_nb_members + 1 WHERE organization_id = '$org_id'";
                    $connection->query($query);
                } else {
                    $return = "Un problème est survenu lors de la validation.";
                }
                header('refresh: 0');
                exit();
            }
        }
    }
    $return = '<p style="color:red;">' .$return .'</p>';
    return $return;
}

/*-------------------------------------------------ADMINISTRATEUR------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/


/*--------------------------------------------INSCRITPION D'UNE ECOLE--------------------------------------------*/

//Fonction d'affichage d'un formulaire decréation d'école
function registerSchoolForm() {
    $return = '<form action="#" method="post">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=2> <h3> Inscrire une école </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nom de l\'école : </td> <td> <input type="text" name="school_name"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Code postal : </td> <td> <input type="text" name="school_postal"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Ville : </td> <td> <input type="text" name="school_city"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Adresse : </td> <td> <input type="text" name="school_address"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2> <input type="submit" name="school_create" value="Inscire mon école">';
    $return .= '</tr> <tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//Fonction de création d'une école
function registerSchool() {
    $connection = db_connection();
    $return = null;
    if(isset($_POST['school_create'])) {
        $school_name = $_POST['school_name'];
        $school_postal = $_POST['school_postal'];
        $school_city = $_POST['school_city'];
        $school_address = $_POST['school_address'];
        if($school_name != "" AND $school_postal != "" AND $school_city != "" AND $school_address != "") {
            $query = "SELECT school_name FROM schools WHERE school_name = '$school_name'";
            $results = $connection->query($query);
            if($results->num_rows == 0) {
                if(is_numeric($school_postal)) {
                    $school_complete_address = $school_address .' ' .$school_postal .' ' .$school_city;
                    $query = "INSERT INTO schools (school_name, school_nb_students, school_nb_organization, school_adress) VALUES ('$school_name', 0, 0, '$school_complete_address')";
                    $results = $connection->query($query);

                    if ($connection->affected_rows == 1) {
                        $return = "Ecole correctement inscrite";
                    } else {
                        $return = "Error: " . $query . "<br>" . $connection->error;
                    }
                    $connection->close();

                } else {
                    $return = "Le code postal doit être composé de chiffres.";
                }
            } else {
                $return = "Une école du même nom est déja inscrite.";
            }
        } else {
            $return = "Veuillez remplir les champs requis.";
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

/*--------------------------------------------INSCRITPION D'UN MANAGER-------------------------------------------*/

//Fonction d'affichage d'un formulaire d'inscription d'un manager
function registerManagerForm() {
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=2> <h3> Enregistrer un manager : </h3>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nom : </td> <td> <input type="text" name="manager_name"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Prénom : </td> <td> <input type="text" name="manager_forename"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Ecole : </td> <td> <input type="text" id="school_id" onkeyup="autocomplet()" name="manager_school" autocomplete="off"> <ul id="school_list_id"> </ul> </td> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Identifiant : </td> <td> <input type="text" name="login"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe : </td> <td> <input type="password" name="pass1"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe : </td> <td> <input type="password" name="pass2"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2> <input type="submit" name="create_manager" value="Valider"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    $return .= '<script type="text/javascript" src="../includes/js/jquery.min.js"></script>';
    $return .= '<script type="text/javascript" src="../includes/js/autocomplete.js"></script>';
    return $return;
}

//Fonction d'inscription d'un manager
function registerManager() {
    $return = null;
    if(isset($_POST['create_manager'])) {
        $manager_name = $_POST['manager_name'];
        $manager_forename = $_POST['manager_forename'];
        $manager_log = $_POST['login'];
        $manager_pass1 = $_POST['pass1'];
        $manager_pass2 = $_POST['pass2'];
        $manager_school = $_POST['manager_school'];
        if($manager_name != "" AND $manager_log != "" AND $manager_pass1 != "" AND $manager_pass2 != "" AND $manager_school != "") {
            if($manager_pass1 === $manager_pass2) {
                $connection = db_connection();
                $query = "SELECT school_id FROM schools WHERE school_name = '$manager_school'";
                $result = $connection->query($query);
                if($result->num_rows > 0){
                    $row = $result->fetch_assoc();
                    $school_id = $row['school_id'];
                } else {
                    return  "L'école recherchée n'existe pas.";
                }
                $query = "SELECT * FROM users WHERE user_log = '$manager_log'";
                $result = $connection->query($query);
                if($result === TRUE) {
                    return "Cet identifiant est déjà utilisé.";
                } else {
                    $query = "INSERT INTO users(user_name, user_forename, user_login, user_pass, user_role, user_school_id) VALUES('$manager_name', '$manager_forename', '$manager_log', '$manager_pass1', 2, '$school_id')";
                    $result = $connection->query($query);
                    if($result === TRUE) {
                        $return = "Inscription de $manager_forename $manager_name réussie.";
                    } else {
                        $return = "Problème survenu lors de la création du compte manager.";
                    }
                }
            } else {
                $return = 'Les mots de passe ne correspondent pas.';
            }
        } else {
            $return = 'Veuillez remplir les champs requis';
        }
        header('location: admin_logged.php');
    }
    $return = '<p style="color:red;">' .$return .'</p>';
    return $return;
}

/*------------------------------------------- GESTION DES UTILISATEURS-------------------------------------------*/

//Fonction d'affichage des utilisateurs
function logList() {
    $connection = db_connection();
    $query = 'SELECT * FROM users INNER JOIN schools ON schools.school_id = users.user_school_id WHERE user_role = 1 OR user_role = 2';
    $result = $connection->query($query);
    if($result->num_rows > 0){
        $return = '<form action="#" method="POST">';
        $return .= '<table>';
        $return .= '<tr> <td colspan = 6> Gérer les logs </td> </tr>';
        $return .= '<tr> <th> Prénom </th> <th> Nom </th> <th> Ecole </th> <th> Login </th> <th> Role </th> <th> Supprimer </th> </tr> ';
        while($row = $result->fetch_assoc()) {
            $return .= '<tr>';
            $return .= '<td>' . $row['user_forename'] .'</td>';
            $return .= '<td>' . $row['user_name'] .'</td>';
            $return .= '<td>' . $row['user_login'] .'</td>';
            if ($row['user_role'] == 1) {
                $return .= '<td> Utilisateur </td>';
            } elseif ($row['user_role'] == 2) {
                $return .= '<td> Manager </td>';
            }
            $return .= '<td>' . $row['school_name'] .'</td>';
            $return .= '<td> <input type="submit" value="Supprimer" name="delete'.$row['user_id'].'"> </td>';
            $return .= '</tr>';
        }
        $return .= '</table>';

        return $return;
    } else {
        return "Il n'y a plus aucun compte utilisateur ou manager";
    }
}

//Fonction de supression des utilisateurs
function deleteLogin() {
    $return = null;
    $connection = db_connection();
    $query = 'SELECT * FROM users INNER JOIN schools ON schools.school_id = users.user_school_id  WHERE user_role = 1 OR user_role = 2';
    $result = $connection->query($query);
    for ($i=0; $i < $result->num_rows; $i++) {
        while($row = $result->fetch_assoc()){
            if(isset($_POST['delete'.$row['user_id']])) {
                $query = 'SELECT * FROM members INNER JOIN organizations ON members.member_organization_id = organizations.organization_id WHERE member_user_id ='.$row['user_id'];
                $result1 = $connection->query($query);
                if ($result1->num_rows > 0) {
                    $row2 = $result1->fetch_assoc();
                    $query = 'DELETE FROM members WHERE member_user_id='.$row['user_id'];
                    $result2 = $connection->query($query);
                    if ($result2 === TRUE) {
                        $query = 'UPDATE organizations SET organization_nb_members = organization_nb_members-1 WHERE organization_id='.$row2['organization_school_id'];
                        $result3 = $connection->query($query);
                        if ($result3 === TRUE) {
                            $query = 'DELETE FROM users WHERE user_id='.$row['user_id'];
                            $result4 = $connection->query($query);
                            if ($result4 === TRUE) {
                                $query = 'UPDATE schools SET school_nb_students = school_nb_students-1 WHERE school_id='.$row['school_id'];
                                $result5 = $connection->query($query);
                                if ($result5 === TRUE) {
                                    header('Refresh: 0');
                                    exit();
                                } else {
                                    $return = "Problème survenu lors de la supression.";
                                }
                            } else {
                                $return = "Problème survenu lors de la supression";
                            }
                        } else {
                            $return = "Problème survenu lors de la supression";
                        }
                    } else {
                        $return = "Problème survenu lors de la supression.";
                    }
                } else {
                    $query = 'DELETE FROM users WHERE user_id='.$row['user_id'];
                    $result4 = $connection->query($query);
                    if ($result4 === TRUE) {
                        $query = 'UPDATE schools SET school_nb_students = school_nb_students-1 WHERE school_id='.$row['school_id'];
                        $result5 = $connection->query($query);
                        if ($result5 === TRUE) {
                            header('Refresh: 0');
                            exit();
                        } else {
                            $return = "Problème survenu lors de la supression.";
                        }
                    } else {
                        $return = "Problème survenu lors de la supression.";
                    }
                }
            }
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

/*----------------------------------------------------DIVERS-----------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------*/

/*---------------------------------------------------AFFICHAGE---------------------------------------------------*/

//Affichage du menu en fonction du type d'utilisateur connecté
function displayMenu() {
    $user_role = $_SESSION['account_role'];
    $return = null;
    switch($user_role) {
        case 1:
        $return .= '<ul>';
        $return .= '<li> <a href="user_display_my_school.php"> Voir mon école </a> </li>';
        $return .= '<li> <a href="user_search_school.php"> Chercher une école </a> </li>';
        $return .= '<li> <a href="user_member_choice.php"> Je suis membre d\'une association </a> </li>';
        $return .= '</ul>';
        break;
        case 2:
        $return .= '<ul>';
        $return .= '<li> <a href="manager_display_my_school.php"> Voir mon école </a> </li>';
        $return .= '<li> <a href="manager_search_school.php"> Chercher une école </a> </li>';
        $return .= '<li> <a href="manager_manage_org.php"> Gérer les demandes associatives </a> </li>';
        $return .= '<li> <a href="manager_manage_member.php"> Gérer les demandes de ralliement </a> </li>';
        $return .= '</ul>';
        break;
        case 3:
        $return .= '<ul>';
        $return .= '<li> <a href="admin_search_school.php"> Chercher une école </a> </li>';
        $return .= '<li> <a href="admin_validate_account.php"> Gérer les utilisateurs  </a> </li>';
        $return .= '<li> <a href="admin_validate_org.php"> Gérer les associations  </a> </li>';
        $return .= '<li> <a href="admin_manage_school.php"> Gérer les écoles  </a> </li>';
        $return .= '</ul>';
        break;
    }
    return $return;
}

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

//Fonction d'affichage du bouton de redirection vers la page de connexion
function displayCreateSchoolButton() {
    $return = '<form action="#" method="POST">';
    $return .= '<input type="submit" name="redirect_create_school" value="Inscire une nouvelle école">';
    $return .= '</form>';
    return $return;
}

//Fonction de redirection vers la page de connexion
function createSchoolButton() {
    if(isset($_POST['redirect_create_school']))
    header('location: admin_create_school.php');
}

//Fonction d'affichage du bouton de redirection vers la page de connexion
function displayCreateManagerButton() {
    $return = '<form action="#" method="POST">';
    $return .= '<input type="submit" name="redirect_create_manager" value="Inscrire un nouveau manager">';
    $return .= '</form>';
    return $return;
}

//Fonction de redirection vers la page de connexion
function createManagerButton() {
    if(isset($_POST['redirect_create_manager']))
    header('location: admin_create_manager.php');
}

/*----------------------------------------AUTORISATION D'ACCES AUX PAGES-----------------------------------------*/

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

/*----------------------------------------------------DIVERS-----------------------------------------------------*/

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
