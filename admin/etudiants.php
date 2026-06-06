<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$result = mysqli_query($conn, "
    SELECT e.*, f.nom_filiere 
    FROM etudiant e 
    LEFT JOIN filiere f ON e.id_filiere = f.id_filiere
");

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EduManager — Étudiants</title>
    <link rel="stylesheet" href="http://localhost:8000/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<script>
function toggleDark() {
    document.body.classList.toggle('dark');
    const icon = document.getElementById('icon-dark');
    if (document.body.classList.contains('dark')) {
        icon.classList.replace('fa-moon', 'fa-sun');
        localStorage.setItem('dark', '1');
    } else {
        icon.classList.replace('fa-sun', 'fa-moon');
        localStorage.setItem('dark', '0');
    }
}

// Appliquer au chargement
if (localStorage.getItem('dark') === '1') {
    document.body.classList.add('dark');
    document.getElementById('icon-dark').classList.replace('fa-moon', 'fa-sun');
}
</script>
<script>
window.addEventListener('load', function() {
    document.getElementById('spinner').style.display = 'none';
});
</script>
<body>
    <div class="layout">

        <?php include 'navbar.php'; ?>

        <div class="main">
            <div class="topbar">
                <span class="topbar-titre">Tableau de bord</span>
                <div class="topbar-user" style="gap:12px;">
                    <!-- Bouton dark mode -->
                    <button class="btn-dark-toggle" onclick="toggleDark()" title="Mode sombre">
                        <i class="fas fa-moon" id="icon-dark"></i>
                    </button>
                    <img src="http://localhost:8000/assets/img/admin.png"
                        style="width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0;">
                    <span><?php echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']; ?></span>
                </div>
            </div>

            <div class="page">
                <div class="page-header">
                    <h2><i class="fas fa-user-graduate"></i> Étudiants</h2>
                    <a href="ajouter_etudiant.php" class="btn btn-primaire">
                        <i class="fas fa-plus"></i> Ajouter un étudiant
                    </a>
                </div>

                <div class="carte">
                    <table>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Filière</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($e = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $e['nom']; ?></td>
                                <td><?php echo $e['prenom']; ?></td>
                                <td><?php echo $e['email']; ?></td>
                                <td><?php echo $e['telephone'] ? $e['telephone'] : '—'; ?></td>
                                <td><?php echo $e['nom_filiere']; ?></td>
                                <td style="display:flex; gap:8px;">
                                    <a href="modifier_etudiant.php?id=<?php echo $e['id_etudiant']; ?>" class="btn btn-secondaire">
                                        <i class="fas fa-pen"></i> Modifier
                                    </a>
                                    <a href="supprimer_etudiant.php?id=<?php echo $e['id_etudiant']; ?>" class="btn btn-danger" onclick="return confirm('Supprimer cet étudiant ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>
<div id="spinner">
    <div class="spinner-cercle"></div>
</div>
</html>