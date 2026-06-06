<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professeur') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$result = mysqli_query($conn, "
    SELECT a.*, e.nom AS nom_etudiant, e.prenom AS prenom_etudiant, m.nom_module
    FROM absence a
    JOIN etudiant e ON a.id_etudiant = e.id_etudiant
    JOIN module m   ON a.id_module   = m.id_module
    ORDER BY a.date_absence DESC
");

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EduManager — Absences</title>
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
                <div class="page-header">
                    <h2><i class="fas fa-calendar-xmark"></i> Absences</h2>
                    <a href="ajouter_absence.php" class="btn btn-danger"><i class="fas fa-plus"></i> Ajouter une absence</a>
                </div>
                <div class="carte">
                    <table>
                        <tr>
                            <th>Étudiant</th>
                            <th>Module</th>
                            <th>Date</th>
                            <th>Justifiée</th>
                            <th>Motif</th>
                            <th>Action</th>
                        </tr>
                        <?php while ($a = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $a['prenom_etudiant'] . ' ' . $a['nom_etudiant']; ?></td>
                                <td><?php echo $a['nom_module']; ?></td>
                                <td><?php echo $a['date_absence']; ?></td>
                                <td>
                                    <?php if ($a['justifiee'] == 1): ?>
                                        <span class="badge-oui">Oui</span>
                                    <?php else: ?>
                                        <span class="badge-non">Non</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $a['motif'] ? $a['motif'] : '—'; ?></td>
                                <td>
                                    <a href="supprimer_absence.php?id=<?php echo $a['id_absence']; ?>" class="btn btn-danger" onclick="return confirm('Supprimer cette absence ?')">
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

</html>