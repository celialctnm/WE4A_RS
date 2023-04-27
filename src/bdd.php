<?php
function connectDb(){
    $host = 'localhost';
    $user = 'root';
    $pwd = 'root';
    $db = 'LEICHTNAM';
    try {
        $bdd = new PDO('mysql:host='.$host.';dbname='.$db.
            ';charset=utf8', $user, $pwd,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        return $bdd;
    } catch (Exception $e) {
        exit('Erreur : '.$e->getMessage());
    }
}
