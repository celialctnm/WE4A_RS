<?php

require_once 'bdd.php';
$bdd = connectDb();

$date =  date('Y-m-d');

$query = $bdd->prepare("SELECT * FROM PUBLICATIONS WHERE IDPUBLICATION = :id");
$query->bindParam(':id', $_GET['id']);
$query->execute();

// déterminer l'utilisateur de la publication
$determineUserPost = $bdd->prepare("SELECT PSEUDO FROM UTILISATEURS INNER JOIN PUBLICATIONS ON PUBLICATIONS.idutilisateur = UTILISATEURS.idutilisateur WHERE IDPUBLICATION = :idpublication");
$determineUserPost->bindParam(':idpublication', $_GET['id']);
$determineUserPost->execute();

$pseudo = "";

foreach ($determineUserPost as $row_userPost){
    $pseudo = $row_userPost['PSEUDO'];
}

$messageErreur = "";

if (isset($_POST['id'])) {
    //session_start();

    //traitement des erreurs pour le fichier
    if (isset($_FILES['image'])){
        $tmpName = $_FILES['image']['tmp_name'];
        $size = $_FILES['image']['size'];
        $error = $_FILES['image']['error'];
        $name = $_FILES['image']['name'];

        if  ($_FILES['image']['size'] != 0 ){
            if($_FILES['image']['size'] > 5242880) {
                $messageErreur = "Fichier trop volumineux, taille max de 5Mo.";
                $uploadIMG = false;
            }
            elseif($_FILES['image']['type'] == "image/jpeg" || $_FILES['image']['type'] == "image/png" || $_FILES['image']['type'] == "image/jpg"){
                $uploadIMG = true;
            }
            else {
                $messageErreur = "Type de fichier non accepté! Images JPEG, JPG et PNG seulement.";
                $uploadIMG = false;
            }
        }
    }

    if ($name != null){

        // rechercher la publication
        $query = $bdd->prepare("SELECT * FROM PUBLICATIONS WHERE IDPUBLICATION = :id");
        $query->bindParam(':id', $_POST['id']);
        $query->execute();

        if ($uploadIMG === true){
            $name = $_SESSION['idutilisateur'].time();
            move_uploaded_file($tmpName, 'upload/'.$name);

            //echo $query->rowCount();
            foreach ($query as $delete_row){
                // supprimer la photo du dossier upload
                unlink('upload/'.$delete_row['IMG']);
            }

            // MAJ de la publication avec image
            $stmt = $bdd->prepare("UPDATE PUBLICATIONS SET DESCRIPTION = :description, CATEGORIE = :categorie, IMG = :img WHERE IDPUBLICATION = :id");
            $stmt->bindParam(':id', $_POST['id']);
            $stmt->bindParam(':description',$_POST['description']);
            $stmt->bindParam(':img', $name);
            $stmt->bindParam(':categorie', $_POST['categorie']);

            $stmt->execute();
            header('Location: ./index.php?user='.$_SESSION['pseudo']);
            exit();
        }

        // MAJ de la publication sans image
        }  else {
        $stmt = $bdd->prepare("UPDATE PUBLICATIONS SET DESCRIPTION = :description, CATEGORIE = :categorie WHERE IDPUBLICATION = :id");
        $stmt->bindParam(':id', $_POST['id']);
        $stmt->bindParam(':description',$_POST['description']);
        $stmt->bindParam(':categorie', $_POST['categorie']);

        $stmt->execute();
        header('Location: ./index.php?user='.$_SESSION['pseudo']);
        exit();

    }


}

?>

<!--- Si aucun n'utilisateur n'est connecté ou que celui-ci n'est pas le propriétaire de l'id, il na pas accès à la page-->
<?php if (isset($_SESSION['pseudo']) && strtoupper($_SESSION['pseudo']) == strtoupper($pseudo)){ ?>

        <div class="modifPost">
            <h2> Éditer une publication </h2>
            <?php if ($messageErreur !== null) { ?>
                <p style="text-align: center; color: #ff1919" id="messageErreur"> <?php echo $messageErreur ?></p>
                <br>
            <?php } ?>
            <form action="index.php?user=<?php echo $_SESSION['pseudo']?>&page=editPost&id=<?php echo $_GET['id']?>" method="post" enctype="multipart/form-data" class="addPost">
                <?php
                foreach ($query as $row){ ?>
                    <div class="gaucheModif">
                        <label> Image </label>
                        <br>
                        <img id="modifImg" src=<?php echo 'upload/'.$row['IMG'] ?>>
                        <br>
                        <input type="file" name="image" id="image">
                    </div>
                    <div class="droiteModif">
                        <input type="hidden" id="id" name="id" value="<?php echo $row['IDPUBLICATION'] ?>">
                        <textarea name="description" id="description" placeholder="Description""><?php echo $row['DESCRIPTION'] ?></textarea>
                        <br>
                        <br>
                        <label> Catégorie </label>
                        <br>

                        <?php
                        if ($row['CATEGORIE'] == "ameriquedunord"){
                            $catSelected = "Amérique du nord";
                        }

                        elseif ($row['CATEGORIE'] == "ameriquedusud"){
                            $catSelected = "Amérique du sud";
                        }

                        else {
                            $catSelected = $row['CATEGORIE'];
                        }
                        ?>

                        <select name="categorie" id="categorie">
                            <option value="<?php echo $row['CATEGORIE']?>" selected="selected" ><?php echo $catSelected?></option>
                            <option value="asie">Asie</option>
                            <option value="ameriquedunord">Amérique du nord</option>
                            <option value="ameriquedusud">Amérique du sud</option>
                            <option value="oceanie">Océanie</option>
                            <option value="afrique">Afrique</option>
                            <option value="europe">Europe</option>
                        </select>
                        <br>
                        <button style="margin-right: auto; margin-left: auto; margin-top: 50px" type="submit"> Enregistrer les modifications </button>
                    </div>
                    <br>
                    <?php
                }
                ?>
            </form>
        </div>




<?php } else { ?>
    <h1 style="text-align: center; color: #ff1919"> Vous n'avez pas accès à cette page </h1>
<?php } ?>



