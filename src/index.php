<html>
<head>
    <title>Voyageurs du monde</title>
    <meta charset="UTF-8">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fjalla+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Khand&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="styles/index.css" rel="stylesheet">
    <link href="styles/allPost.css" rel="stylesheet">
    <link href="styles/post.css" rel="stylesheet">
    <link href="styles/header.css" rel="stylesheet">
    <link href="styles/title.css" rel="stylesheet">
    <link href="styles/addPost.css" rel="stylesheet">

</head>
<body>


<?php
    session_start();
    require_once 'bdd.php';
    $bdd = connectDb();
?>

<div>

    <?php
        include 'title.php';
        include 'header.php';
    ?>

    <?php


    //si utilisateur connecté + click bouton créer publication, on affiche la page pour créer un post
    if ((isset($_SESSION['pseudo'])) && isset($_GET['page']) && ($_GET['page']== "addPost")) {
        include 'addPost.php';
    }

    //si utilisateur connecté + click bouton modifier post, on affiche la page pour modifier un post
    elseif ((isset($_SESSION['pseudo'])) && isset($_GET['page']) && ($_GET['page']== "editPost")){
        include 'modifPost.php';
    }

    //si utilisateur connecté + click supprimer post, on affiche la page pour supprimer un post
    elseif ((isset($_SESSION['pseudo'])) && isset($_GET['page']) && ($_GET['page']== "supprPost")){
        include 'supprPost.php';
    }

    // dans tous les autres cas, la page AllPost affiche tous les posts + les posts en fonction de la catégorie
    // traitement de la barre de recherche également
    else {
        include 'allPost.php';
    }
    ?>


</div>




</body>
</html>