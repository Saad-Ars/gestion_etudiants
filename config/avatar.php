<?php
// Retourne l'image de profil selon le rôle connecté
function getAvatar() {
    $role = $_SESSION['role'] ?? 'admin';
    $images = [
        'admin'      => 'admin.png',
        'professeur' => 'professeur.png',
        'etudiant'   => 'etudiant.png',
    ];
    return 'http://localhost:8000/assets/img/' . ($images[$role] ?? 'admin.png');
}
?>  