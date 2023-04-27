<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <link href="styles/index.css" rel="stylesheet">

</head>
<body>

<?php
$messageErreur = "";

    function SecurizeString_ForSQL($string) {
        $string = trim($string);
        $string = stripcslashes($string);
        $string = addslashes($string);
        $string = htmlspecialchars($string);
        return $string;
    }

    require_once 'bdd.php';
    $bdd = connectDb();

    $loginSuccessful = false;

    // sécuriser les champs remplis
    if(isset($_POST["pseudo"]) && isset($_POST["password"])){
            $pseudo = SecurizeString_ForSQL($_POST["pseudo"]);
            $password = SecurizeString_ForSQL($_POST["password"]);
        };

    // si le pseudo rentré existe dans la bdd, on le stocke dans la variable pseudo
    if (isset($pseudo) && isset($password)){
        $query = $bdd->prepare('SELECT IDUTILISATEUR, MDP, AVATAR FROM UTILISATEURS WHERE PSEUDO=:pseudo LIMIT 1');
        $query->execute(array(
                'pseudo' => $pseudo
        ));

        // si l'utilisateur n'existe pas, message d'erreur
        if ($query->rowCount()==0){
            $messageErreur = "Identifiant ou mot de passe incorrect";
        }

        // si pseudo trouvé
        elseif ($query->rowCount()==1){
            $passwordVerif = "";
            $avatar = "";
            $idutilisateur = 0;

            foreach ($query as $row) {
                $idutilisateur = $row['IDUTILISATEUR'];
                $passwordVerif = $row['MDP'];
                $avatar = $row['AVATAR'];
            }

            // on compare le mdp rentré avec celui de la bdd
            if (password_verify($password, $passwordVerif)){
                $loginSuccessful = true;
            }
            else{
                $messageErreur = 'Identifiant ou mot de passe incorrect';
                $loginSuccessful = false;
            }

            }
        }



    // si tout a été vérifié et est valide
    if ($loginSuccessful == true) {

        // on démarre une session pour stocker les éléments de l'utimisateur
        session_start();

        $_SESSION['pseudo'] = $pseudo;
        $_SESSION['avatar'] = $avatar;
        $_SESSION['idutilisateur'] = $idutilisateur;

        // redirection sur sa page perso
        header('Location: ./index.php?user='.$_SESSION['pseudo'].'&page=moncompte');

        exit();
    }

?>
<div class="creation_post">
    <div class="form_login">
        <form action="./login.php" method="post">
            <h2> Welcome back </h2>
            <br>
            <input type="text" name="pseudo" id="pseudo" placeholder="Nom d'utilisateur" required>
            <br>
            <input type="password" name="password" id="password" placeholder="Mot de passe" required>
            <br>
            <?php if ($messageErreur !== null) { ?>
                <p id="messageErreur"> <?php echo $messageErreur ?></p>
            <?php } ?>
            <button type="submit"> Se connecter </button>
        </form>
        <a href="./inscription.php"> Vous n'avez pas de compte ? <span>Inscrivez-vous</span></a>
    </div>
    <div class="login_img">
        <img src="img/login.jpeg">
    </div>
</div>


</body>
</html>