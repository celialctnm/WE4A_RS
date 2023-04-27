<?php

    // Connexion à la base de données
    require_once 'bdd.php';
    $bdd = connectDb();

    // récupérer toutes les publications en fct d'un user
    if (isset($_GET['user'])){
        $stmt = $bdd->prepare("SELECT * FROM PUBLICATIONS INNER JOIN UTILISATEURS ON UTILISATEURS.idutilisateur = publications.idutilisateur WHERE UTILISATEURS.pseudo = :pseudo ORDER BY IDPUBLICATION DESC");
        $stmt->bindParam(':pseudo',$_GET['user']);
    }

    // récupérer toutes les publications en fct de la catégorie choisie
    elseif (isset($_GET['categorie'])){
        $stmt = $bdd->prepare("SELECT * FROM PUBLICATIONS INNER JOIN UTILISATEURS ON UTILISATEURS.idutilisateur = publications.idutilisateur WHERE CATEGORIE= :categorie ORDER BY IDPUBLICATION DESC");
        $stmt->bindParam(':categorie',$_GET['categorie']);
    }

    // récupérer toutes les publications contenant les éléments tapées dans la barre de recherche
    elseif (isset($_POST['recherche'])){
        $stmt = $bdd->prepare("SELECT * FROM PUBLICATIONS INNER JOIN UTILISATEURS ON UTILISATEURS.idutilisateur = publications.idutilisateur WHERE PSEUDO LIKE :value");
        $value = "%".$_POST['recherche']."%";
        $stmt->bindParam(':value',$value);

    }

    else {
        // Récupérer les 12 publications les + récentes
        $stmt = $bdd->prepare("SELECT * FROM PUBLICATIONS INNER JOIN UTILISATEURS ON UTILISATEURS.idutilisateur = publications.idutilisateur ORDER BY IDPUBLICATION DESC LIMIT 12");
    }

    //file size à faire
    // exécuter la requête
    $stmt->execute();


?>

<link href="styles/post.css" rel="stylesheet">
<main>

    <?php if (isset($_SESSION['pseudo']) && (isset($_GET['user']) && $_SESSION['pseudo'] == $_GET['user']) && isset($_GET['page']) && ($_GET['page'] == 'moncompte')) { ?>

        <?php

        $bio = $bdd->prepare("SELECT PAYS FROM UTILISATEURS WHERE PSEUDO = :pseudo");
        $bio->bindParam(':pseudo', $_SESSION['pseudo']);
        $bio->execute();
        $bio_txt = "";
        foreach ($bio as $bio_row){
            $bio_txt = $bio_row['PAYS'];
        }

        ?>
        <div class="entete_user">
            <div class="entete_img">
                <img src=<?php echo 'upload/'.$_SESSION['avatar'] ?>>
            </div>
            <div class="bio">
                <h2><?php echo $_SESSION['pseudo']?></h2>
                <h4> Prochain voyage : <span id="demo"><?php echo $bio_txt?></span></h4>
                <button class="voyage" onclick="updateBio()">Définir un voyage</button>
                <script>

                    function updateBio(){
                        // MAJ dynamique du voyage saisi dans l'input du prompt
                        let text;
                        let person = prompt("Saississez votre prochain voyage :", "");
                        var xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function() {
                            if (this.readyState === 4 && this.status === 200) {
                                document.getElementById("demo").innerHTML = this.responseText;
                            }
                        };

                        if (person == null || person === "") {
                            text = "<?php echo $bio_txt ?>";
                        } else {
                            text = person;
                        }

                        // appel de la page updateBio
                        xmlhttp.open("GET","updateBio.php?pays="+person,true);
                        xmlhttp.send();


                        }
                </script>
            </div>

        </div>
    <?php } ?>

    <!-- Si barre de recherche utilisée -->
    <?php if (isset($_POST['recherche'])){
        $user = $bdd->prepare("SELECT * FROM UTILISATEURS WHERE PSEUDO LIKE :value");
        $value = "%".$_POST['recherche']."%";
        $user->bindParam(':value',$value);
        $user->execute();

        // regarder si la recherche correspond à un utilisateur

        if ($user->rowCount() == 0){ ?>
            <h4> Aucun utilisateur ne correspond à votre recherche </h4>
        <?php }

        // afficher le nom, l'avatar et le futur voyage des personnes correspondant à la recherche
        foreach ($user as $row_user ){ ?>
                <div class="entete_user">
                    <div class="entete_img">
                        <img src=<?php echo 'upload/'.$row_user['AVATAR'] ?>>
                    </div>
                    <div class="bio">
                            <h2><?php echo $row_user['PSEUDO']?></h2>
                            <h4> Prochain voyage : <span id="demo"><?php echo $row_user['PAYS']?></span></h4>
                            <a style="text-decoration: none; color: #0e2ab4" href="<?php echo './index.php?user='.$row_user['PSEUDO'] ?>">Voir les publications</a>
                    </div>
                </div>
        <?php }

        ?>

    <?php } else {
        // $row_post = éléments des posts
    foreach ($stmt as $row_post) {

        // compter le nbr de like pour chaque publication
        $likes = $bdd->prepare("SELECT COUNT(IDLIKE) AS NBR_LIKES FROM LIKES WHERE IDPUBLICATION = :id GROUP BY IDPUBLICATION;");
        $likes->bindParam(':id',$row_post['IDPUBLICATION']);
        $likes->execute();


        // regarder si l'utilisateur a déjà liké
        $verifLike = $bdd->prepare("SELECT * FROM LIKES WHERE IDPUBLICATION = :idpublication AND IDUTILISATEUR = :idutilisateur ");
        $verifLike->bindParam(':idutilisateur',$_SESSION['idutilisateur']);
        $verifLike->bindParam(':idpublication', $row_post['IDPUBLICATION']);
        $verifLike->execute();

        ?>

            <div class="post">
                <div class="entete">
                    <p class="p_pseudo"><img id="avatar" src=<?php echo 'upload/'.$row_post['AVATAR'] ?>> <?php echo $row_post['PSEUDO']?></p>

                    <?php
                    // utilisateur connecté et que c'est son post = suppression + modification possible
                    if (isset($_SESSION['pseudo']) && strtoupper($_SESSION['pseudo']) == strtoupper($row_post['PSEUDO'])){ ?>
                        <a id="iconmodifier" href=index.php?user=<?php echo $row_post['PSEUDO']?>&page=editPost&id=<?php echo $row_post['IDPUBLICATION']?>><img src="img/edit.png"></a>
                        <a id="icondelete" onclick="return confirm('Supprimer cette publication ?')" href="index.php?user=<?php echo $row_post['PSEUDO']?>&page=supprPost&id=<?php echo $row_post['IDPUBLICATION']?>"><img src="img/delete.png"></a>
                        <?php
                    }
                    ?>
                </div>
                <div class="image">
                    <img src=<?php echo 'upload/'.$row_post['IMG'] ?>>
                </div>
                <div class="description">

                    <div class="likes">
                        <?php if ($likes->rowCount() === 0){
                            // si il n'y a aucun like sur un post, on affiche zéro like
                            ?>
                            <p style="margin-top: 2%; padding-right: 5px" id="<?php echo $row_post['IDPUBLICATION']?>">0</p>

                            <?php if (isset($_SESSION['pseudo'])){
                                // si un utilisateur est connecté => coeur vide + action pour aimer le post
                                ?>
                                <img onclick="showUser(<?php echo 'likes'.$row_post['IDPUBLICATION']?>)" id="<?php echo 'likes'.$row_post['IDPUBLICATION']?>" style="height: 2em" src="img/vide_like.png">
                            <?php } else {
                                // si un utilisateur n'est pas connecté => coeur vide mais aucune action onclick
                                ?>
                                <img style="height: 2em" src="img/vide_like.png">
                            <?php } ?>

                        <?php } else {
                            foreach ($likes as $row_likes) {
                                // afficher précisément le nbr de like pour chaque post si il y en a déjà un
                                ?>
                                <p style="margin-top: 2%; padding-right: 5px" id="<?php echo $row_post['IDPUBLICATION']?>"><?php echo $row_likes['NBR_LIKES']?></p>
                                <?php if (isset($_SESSION['pseudo'])){
                                    if ($verifLike->rowCount() !== 0){
                                        // si l'utilisateur à déjà liké ce post =>  coeur plein
                                        ?>
                                        <img onclick="showUser(<?php echo 'likes'.$row_post['IDPUBLICATION']?>)" id="<?php echo 'likes'.$row_post['IDPUBLICATION']?>" style="height: 2em" src="img/plein_like.png">
                                    <?php }
                                    else {
                                        // l'utilisateur n'a pas liké => coeur vide
                                        ?>
                                        <img onclick="showUser(<?php echo 'likes'.$row_post['IDPUBLICATION']?>)" id="<?php echo 'likes'.$row_post['IDPUBLICATION']?>" style="height: 2em" src="img/vide_like.png">
                                        <?php
                                        }
                                } else {
                                    ?>
                                    <img style="height: 2em" src="img/vide_like.png">
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <p id="legende"><span><?php echo $row_post['PSEUDO']?></span> <?php echo " ".$row_post['DESCRIPTION'] ?>
                        <?php
                        // Détecter si la catégorie existe
                        if ($row_post['CATEGORIE'] != null){ ?>
                        <span id="categorieDescription">#<?php echo $row_post['CATEGORIE']?></span>
                        <?php } ?>
                        <br>
                        <p id="datePost"> <?php echo $row_post['DATE']?></p>
                    </p>

                </div>
            </div>
            <?php
    }
    ?>

    <script type="text/javascript">

        function showUser(str) {

            //console.log("ID : ", str);
            //console.log("STR : ", document.getElementById(str.id.slice(5)));
            //console.log("TEST : ", document.getElementById('test'));
            // requête ajax
            if (str === "") {
                document.getElementById(str.id.slice(5)).innerHTML = "";

            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        document.getElementById(str.id.slice(5)).innerHTML = this.responseText;
                        //document.getElementById(str.id).innerHTML = this.responseText;
                    }
                };

                // variables à modifier dynamiquement = image coeur + nbr de like
                var idToChange = str.id.slice(5);
                var imageToChange = document.getElementById(str.id);


                // changer les valeurs des variables en live
                imageToChange.src = "img/plein_like.png";

                // récupérer seulement l'id sans le str 'likes'
                xmlhttp.open("GET","likes.php?id="+idToChange,true);
                xmlhttp.send();

            }
        }
    </script>
  <?php  } ?>




</main>