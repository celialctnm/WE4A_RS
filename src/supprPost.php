<?php

require_once 'bdd.php';
$bdd = connectDb();

// supprimer les likes de la publication
$delete_like = $bdd->prepare("DELETE FROM LIKES WHERE IDPUBLICATION = :id");
$delete_like->bindParam(':id',$_GET['id'] );
$delete_like->execute();

// supprimer la photo du dossier upload
$delete_file = $bdd->prepare("SELECT * FROM PUBLICATIONS WHERE IDPUBLICATION = :id");
$delete_file->bindParam(':id',$_GET['id'] );
$delete_file->execute();

//opendir('upload/');
//echo 'NBR LIGNE : ', $delete_file->rowCount();
foreach ($delete_file as $delete_row){
    // supprimer la photo du dossier upload
    unlink('upload/'.$delete_row['IMG']);
}

// supprimer la publication
$stmt = $bdd->prepare("DELETE FROM PUBLICATIONS WHERE IDPUBLICATION = :id");
$stmt->bindParam(':id',$_GET['id'] );
$stmt->execute();

// redirection vers la page de son compte
header('Location: ./index.php?user='.$_SESSION['pseudo']."&page=moncompte");
exit();