<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM etudiant WHERE id_etudiant='$id'");

header("Location: etudiants.php");
exit();
?>