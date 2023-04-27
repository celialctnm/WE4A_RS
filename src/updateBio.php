<?php

require_once 'bdd.php';
$bdd = connectDb();

$pays = $_GET['pays'];
session_start();

// mettre à jour le pays saisi si l'utillisateur a rentré une info
if ($_GET['pays'] !== 'null'){
    $bio = $bdd->prepare("UPDATE UTILISATEURS SET PAYS = :pays WHERE PSEUDO = :pseudo");
    $bio->bindParam(':pays', $pays);
    $bio->bindParam(':pseudo', $_SESSION['pseudo']);
    $bio->execute();
    echo $pays;
} else {
    // sinon récupérer le pays initial
    $stmt_pays = $bdd->prepare("SELECT PAYS FROM UTILISATEURS WHERE PSEUDO = :pseudo");
    $stmt_pays->bindParam(':pseudo', $_SESSION['pseudo']);
    $stmt_pays->execute();

    $var = "";
    foreach ($stmt_pays as $row_pays){
        $var = $row_pays['PAYS'];
    }
    echo $var;
}



