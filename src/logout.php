<?php

// détruire la session
session_start();
session_unset();
session_destroy();

// redirection sur l'index
header('Location: index.php');
die();

