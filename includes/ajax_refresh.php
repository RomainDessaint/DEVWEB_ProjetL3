<?php

// PDO connect
function connect() {
    return new PDO('mysql:host=localhost;port=3306;dbname=db_clm', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

$pdo = connect();
$keyword = '%'.$_POST['keyword'].'%';
$sql = "SELECT * FROM schools WHERE school_name LIKE (:keyword)";
$query = $pdo->prepare($sql);
$query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
$query->execute();
$list = $query->fetchAll();
foreach ($list as $rs) {
    // put in bold the written text
    $school_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['school_name']);
    // add new option
    echo '<li class="input_container" onclick="set_item(\''.$rs['school_name'].'\')">'.$school_name.'</li>';
}
?>
