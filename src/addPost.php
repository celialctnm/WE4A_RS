<?php

require_once 'bdd.php';
$bdd = connectDb();

$date =  date('Y-m-d');
$messageErreur = "";
$uploadIMG = false;

// sécuriser les chaînes de caractères pour éviter les injections SQL
function SecurizeString_ForSQL($string) {
    $string = trim($string);
    $string = stripcslashes($string);
    $string = addslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}
// si il y a une description, une catégorie et une image
if (isset($_POST['description']) && isset($_POST['categorie']) && isset($_FILES['image'])) {

    // contrôle des erreurs files
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

    $description = SecurizeString_ForSQL($_POST['description']);

    if ($messageErreur == null){

        // on renomme le fichier tel quel : idutilisateur + temps (depuis 1er janvier 1970)
        $name = $_SESSION['idutilisateur'].time();

        //on ajoute le fichier dans le dossier upload
        move_uploaded_file($tmpName, 'upload/'.$name);

        // insérer les valeurs récupérées dans la table PUBLICATION
        $stmt = $bdd->prepare("INSERT INTO PUBLICATIONS(DATE, DESCRIPTION, CATEGORIE, IMG, IDUTILISATEUR) VALUES (:date, :description, :categorie, :img, :id )");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':categorie', $_POST['categorie']);
        $stmt->bindParam(':img', $name);
        $stmt->bindParam(':id', $_SESSION['idutilisateur']);
        $stmt->execute();


        // redirection vers la page de l'utilisateur
        header('Location: ./index.php?user='.$_SESSION['pseudo']);
        exit();
    }


}


?>

<?php if (isset($_SESSION['pseudo'])) { ?>
        <div class="creerPost">
            <form action="index.php?page=addPost" method="post" enctype="multipart/form-data" class="addPost">
                <h2> CRÉER UNE PUBLICATION </h2>
                <textarea name="description" id="description" placeholder="Légende de votre publication" required></textarea>
                <br>
                <label> Catégorie </label>
                <br>
                <select name="categorie" id="categorie" required>
                    <option value="asie" selected="selected">Asie</option>
                    <option value="ameriquedunord">Amérique du nord</option>
                    <option value="ameriquedusud">Amérique du sud</option>
                    <option value="oceanie">Océanie</option>
                    <option value="afrique">Afrique</option>
                    <option value="europe">Europe</option>
                </select>
                <br>
                <br>
                <label> Image </label>
                <br>
                <br>
                <input type="file" name="image" id="image" required>
                <?php if ($messageErreur !== null) { ?>
                    <p id="messageErreur"> <?php echo $messageErreur ?></p>
                <?php } ?>
                <button id="btnAddPost" type="submit"> Publier </button>
            </form>
        </div>



<?php } else { ?>
    <h1 style="text-align: center; color: #ff1919"> Vous n'avez pas accès à cette page </h1>
<?php } ?>

