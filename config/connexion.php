<?php
$hote     = "localhost";
$user     = "root";
$pass     = "";
$base     = "gestion_etudiants";

$conn = mysqli_connect($hote, $user, $pass, $base);

if (!$conn) {
    die("Erreur : " . mysqli_connect_error());
}
?>