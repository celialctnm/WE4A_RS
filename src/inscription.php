<html>
<head>
    <title>Inscription</title>
    <meta charset="UTF-8">
    <link href="styles/index.css" rel="stylesheet">
    <link href="styles/inscription.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

</head>
<body>

<?php

// connection à la bdd
require_once "bdd.php";
$bdd = connectDb();

$uploadIMG = false;
$inscriptionSucess = false;
$messageErreur = "";

// sécuriser les chaînes de caractères pour éviter les injections SQL
function SecurizeString_ForSQL($string) {
    $string = trim($string);
    $string = stripcslashes($string);
    $string = addslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

// si tous les champs sont remplis, aucun message d'erreur
if ((isset($_POST['pseudo'])) && isset($_POST['password']) && isset($_POST['passwordConfirm']) && isset($_FILES['avatar']) && isset($_POST['mail'])){
    $messageErreur = "";
}

// si le pseudo n'est pas vide
if (isset($_POST['pseudo'])){
    // on sécurise le pseudo
    $pseudo = SecurizeString_ForSQL($_POST['pseudo']);

    // on vérifie que le pseudo est disponible
    $sql = $bdd->prepare("SELECT * FROM UTILISATEURS WHERE PSEUDO = :pseudo");
    $sql->bindParam(':pseudo', $pseudo);
    $sql->execute();

    // on compte le nbr de ligne return
    if ($sql->rowCount() !== 0){
        $messageErreur = 'Ce pseudo existe déjà, veuillez en choisir un autre';
    }

    // si une image pour l'avatar est uploadé
    if (isset($_FILES['avatar'])){
        $tmpName = $_FILES['avatar']['tmp_name'];
        $name = SecurizeString_ForSQL("IMG".$pseudo);
        $size = $_FILES['avatar']['size'];
        $error = $_FILES['avatar']['error'];

        // on regarde que la taille n'est pas égale à 0
        if  ($_FILES['avatar']['size'] != 0 ){
            // la taille ne doit pas dépasser 5mo
            if($_FILES['avatar']['size'] > 5242880) {
                $messageErreur = "Fichier trop volumineux, taille max de 5Mo.";
                $uploadIMG = false;
            }
            //vérifier que le format est bon, ici dans le cas d'une photo
            elseif($_FILES['avatar']['type'] == "image/jpeg" || $_FILES['avatar']['type'] == "image/png" || $_FILES['avatar']['type'] == "image/jpg"){
                $uploadIMG = true;
            }
            else {
                $messageErreur = "Type de fichier non accepté! Images JPEG, JPG et PNG seulement.";
                $uploadIMG = false;
            }
        }
        else {
            // si pas de file or file = 0, erreur
            $messageErreur = "No file or file size = 0";
            $uploadIMG = false;
        }
    }
}


if(isset($_POST["pseudo"]) && isset($_POST["password"]) && isset($_FILES['avatar'])){
    $pseudo = SecurizeString_ForSQL($_POST['pseudo']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $passwordConfirm = $_POST['passwordConfirm'];

    //vérification des champs mdp et mdp à confirmer
    if (isset($password) && isset($passwordConfirm) && $password !== null && $passwordConfirm != null ){
        if (password_verify($passwordConfirm, $password)) {
            $inscriptionSucess = true;
        } else {
            $messageErreur = "Les mots de passe ne correspondent pas";
        }
    } else {
        $messageErreur = "Les mots de passe sont vides";
    }
}


// si tout est ok, on peut créer l'utilisateur
if ($messageErreur == null && $inscriptionSucess == true) {
    $stmt = $bdd->prepare("INSERT INTO UTILISATEURS (pseudo, mail, mdp, avatar) VALUES (:pseudo, :mail, :password, :avatar)");

    // si l'upload est ok, on renomme le fichier comme : pseudo + 'avatar'
    if ($uploadIMG == true){
        $tmpName = $_FILES['avatar']['tmp_name'];
        move_uploaded_file($tmpName, 'upload/'.$pseudo.'avatar');
    }

    $name = $pseudo.'avatar';

    //execution de l'insert dans la bdd
    $stmt->bindParam(':pseudo', $pseudo);
    $stmt->bindParam(':mail', $_POST['mail']);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':avatar', $name);

    $stmt->execute();
    // on récupère l'ID de l'utilisateur qui pourra être utlisé par la suite
    $id = $bdd->lastInsertId();

    // on démarre une session => utilisateur est connecté
    session_start();

    // on stocke les éléments importans de l'utilisateur afin de les récupérer + facilement
    $_SESSION['pseudo'] = $_POST['pseudo'];
    $_SESSION['avatar'] = $name;
    $_SESSION['idutilisateur'] = $id;


    // redirection sur la page de l'utilisateur
    header('Location: ./index.php?user='.$_SESSION['pseudo'].'&page=moncompte');
    exit();

}





?>

<div class="main_inscription">
    <div class="form_inscription">
        <form action="./inscription.php" method="post" enctype="multipart/form-data">
            <h2> Inscription </h2>
            <br>
            <input type="text" name="pseudo" id="pseudo" placeholder="Pseudo">
            <br>
            <input type="email" name="mail" id="mail" placeholder="Adresse mail">
            <br>
            <label style="margin-right: 16em; color: rgba(0, 0, 139, 0.65);"> Avatar </label>
            <br>
            <input type="file" name="avatar" id="avatar">
            <br>
            <input type="password" name="password" id="password" placeholder="Mot de passe">
            <br>
            <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirmer le mot de passe">
            <br>
            <?php

            if (($messageErreur != "")){
                // si on a un message d'erreur on l'affichera au moment du submit
                ?>
                <p style="color: red"> <?php echo $messageErreur?></p>
            <?php } ?>
            <button type="submit"> S'inscrire </button>
        </form>
        <a href="./login.php"> Vous avez déjà un compte ? <span>Connectez-vous</span></a>
    </div>
    <div class="inscription_img">
        <img src="img/login.jpeg">
    </div>
</div>


</body>
</html>
