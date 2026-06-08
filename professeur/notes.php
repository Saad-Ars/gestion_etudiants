<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professeur') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$result = mysqli_query($conn, "
    SELECT n.*, e.nom AS nom_etudiant, e.prenom AS prenom_etudiant,
           m.nom_module, a.libelle AS annee
    FROM note n
    JOIN etudiant e ON n.id_etudiant = e.id_etudiant
    JOIN module m   ON n.id_module   = m.id_module
    JOIN annee_academique a ON n.id_annee = a.id_annee
    ORDER BY n.id_note DESC
");

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EduManager — Notes</title>
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
</script><script>
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
                    <img src="http://localhost:8000/assets/img/professeur.png"
                        style="width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0;">
                    <span><?php echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']; ?></span>
                </div>
            </div>
            <div class="page">
                <div class="page-header">
                    <h2><i class="fas fa-star"></i> Notes</h2>
                    <a href="ajouter_note.php" class="btn btn-succes"><i class="fas fa-plus"></i> Ajouter une note</a>
                </div>
                <div class="carte">
                    <table>
                        <tr>
                            <th>Étudiant</th>
                            <th>Module</th>
                            <th>Contrôle</th>
                            <th>Examen</th>
                            <th>Moyenne</th>
                            <th>Session</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($n = mysqli_fetch_assoc($result)):
                            $moy = ($n['note_controle'] + $n['note_examen'] * 2) / 3;
                        ?>
                            <tr>
                                <td><?php echo $n['prenom_etudiant'] . ' ' . $n['nom_etudiant']; ?></td>
                                <td><?php echo $n['nom_module']; ?></td>
                                <td><?php echo $n['note_controle']; ?>/20</td>
                                <td><?php echo $n['note_examen']; ?>/20</td>
                                <td style="font-weight:600; color:<?php echo $moy >= 10 ? '#059669' : '#dc2626'; ?>">
                                    <?php echo number_format($moy, 2); ?>/20
                                </td>
                                <td><?php echo $n['session']; ?></td>
                                <td>
                                    <a href="modifier_note.php?id=<?php echo $n['id_note']; ?>" class="btn btn-secondaire"><i class="fas fa-pen"></i> Modifier</a>
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