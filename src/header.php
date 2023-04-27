<div class="header">


    <?php

        if (isset($_SESSION['pseudo']) == false){ ?>
            <div class="connexion_inscription">
                <div class="barre_recherche">
                    <form action="./index.php" method="post" enctype="multipart/form-data" >
                        <input type="search" name="recherche" placeholder="Rechercher un utilisateur">
                        <button id="submit_r" type="submit"><img id="loupe" src="img/loupe.png"></button>
                    </form>
                </div>
                <div class="buttons">
                    <a href="login.php"><button class="btn_connexion"> Se connecter </button></a>
                    <a href="inscription.php"><button class="btn_inscription"> S'inscrire </button></a>
                </div>
            </div>

    <?php

        }

        else { ?>
            <div class="connexion_inscription">
                <div class="barre_recherche">
                    <form action="./index.php" method="post" enctype="multipart/form-data" >
                        <input type="search" name="recherche" placeholder="Rechercher un utilisateur">
                        <button id="submit_r" type="submit"><img id="loupe" src="img/loupe.png"></button>
                    </form>
                </div>
                <div class="buttons">
                    <a href="index.php?page=addPost"><button class="btn_add">Créer une publication</button></a>
                    <a href="logout.php"><button class="btn_deconnexion"> Déconnexion </button></a>
                </div>
            </div>
    <?php
        }

    ?>

    <div class="box_amis">

        <?php
        // si utilisateur connecté on affiche l'icone pour accèder à ses posts

        if (isset($_SESSION['pseudo'])){ ?>
            <div class="box">
                <a href=<?php echo './index.php?user='.$_SESSION['pseudo'].'&page=moncompte'?>>
                    <img class="amis" src=<?php echo 'upload/'.$_SESSION['avatar'] ?>>
                    <p> MON COMPTE </p>
                </a>
            </div>
        <?php
            }
        ?>

        <div class="box">
            <a href="./index.php">
                <img class="amis" src="img/plane.png">
                <p> FIL D'ACTUALITÉ </p>
            </a>
        </div>

        <div class="box">
            <a href="./index.php?categorie=asie">
                <img class="amis" src="img/asie.jpeg">
                <p> ASIE </p>
            </a>
        </div>

        <div class="box">
            <a href="./index.php?categorie=ameriquedunord">
                <img class="amis" src="img/ameriquenord.jpg">
                <p> AMÉRIQUE DU NORD </p>
            </a>
        </div>

        <div class="box">
            <a href="./index.php?categorie=ameriquedusud">
                <img class="amis" src="img/ameriquesud.jpg">
                <p> AMÉRIQUE DU SUD </p>
            </a>
        </div>

        <div class="box">
            <a href="./index.php?categorie=oceanie">
                <img class="amis" src="img/oceanie.jpg">
                <p> OCÉANIE </p>
            </a>
        </div>

        <div class="box">
            <a href="./index.php?categorie=afrique">
                <img class="amis" src="img/afrique.jpg">
                <p> AFRIQUE </p>
            </a>
        </div>

        <div class="box">
            <a href="./index.php?categorie=europe">
                <img class="amis" src="img/europe.jpg">
                <p> EUROPE </p>
            </a>
        </div>



    </div>

</div>



</body>
</html>