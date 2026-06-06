<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professeur') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$id_prof = $_SESSION['id'];

$r1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM etudiant");
$nb_etudiants = mysqli_fetch_assoc($r1)['total'];

$r2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM note");
$nb_notes = mysqli_fetch_assoc($r2)['total'];

$r3 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM absence");
$nb_absences = mysqli_fetch_assoc($r3)['total'];

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>GestEtud — Professeur</title>
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
                <h2 style="margin-bottom:24px;">Bonjour, <?php echo $_SESSION['prenom']; ?> 👋</h2>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#eff6ff;">
                            <i class="fas fa-user-graduate" style="color:#2563eb;"></i>
                        </div>
                        <div class="stat-info">
                            <div class="nombre" style="color:#2563eb;"><?php echo $nb_etudiants; ?></div>
                            <div class="label">Étudiants</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#ecfdf5;">
                            <i class="fas fa-star" style="color:#059669;"></i>
                        </div>
                        <div class="stat-info">
                            <div class="nombre" style="color:#059669;"><?php echo $nb_notes; ?></div>
                            <div class="label">Notes saisies</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#fef2f2;">
                            <i class="fas fa-calendar-xmark" style="color:#dc2626;"></i>
                        </div>
                        <div class="stat-info">
                            <div class="nombre" style="color:#dc2626;"><?php echo $nb_absences; ?></div>
                            <div class="label">Absences</div>
                        </div>
                    </div>
                </div>

                <div class="carte">
                    <h3><i class="fas fa-bolt" style="color:#d97706;"></i> Accès rapide</h3>
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <a href="notes.php" class="btn btn-succes"><i class="fas fa-star"></i> Gérer les notes</a>
                        <a href="absences.php" class="btn btn-danger"><i class="fas fa-calendar-xmark"></i> Gérer les absences</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>