<?php

require_once 'bdd.php';
$bdd = connectDb();

// récupérer l'id de la publication qu'on veut liké
$id = intval($_GET['id']);
session_start();

// Savoir si un utilisateur a liké une publication donnée
$verifLike = $bdd->prepare("SELECT * FROM LIKES WHERE IDPUBLICATION = :idpublication AND IDUTILISATEUR = :idutilisateur ");
$verifLike->bindParam(':idutilisateur',$_SESSION['idutilisateur']);
$verifLike->bindParam(':idpublication', $id);
$verifLike->execute();
$result = $verifLike->rowCount();

// S'il ne l'a pas liké, on ajoute son like
if ($result == 0){
    $add = $bdd->prepare("INSERT INTO LIKES(IDUTILISATEUR, IDPUBLICATION) VALUES (:idutilisateur, :idpublication)");
    $add->bindParam(':idutilisateur',$_SESSION['idutilisateur']);
    $add->bindParam(':idpublication', $id);
    $add->execute();
}

// Récupérer la version MAJ du nombre de like pour la publication
$likes = $bdd->prepare("SELECT COUNT(IDLIKE) AS NBR_LIKES FROM LIKES WHERE IDPUBLICATION = :id GROUP BY IDPUBLICATION;");
$likes->bindParam(':id',$id);
$likes->execute();

foreach ($likes as $row_likes){
    $nbr = $row_likes['NBR_LIKES'];
}

// afficher dans le HTML la version actualisée des likes
echo $nbr;