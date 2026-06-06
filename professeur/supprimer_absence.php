<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professeur') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM absence WHERE id_absence='$id'");
header("Location: absences.php");
exit();
?>