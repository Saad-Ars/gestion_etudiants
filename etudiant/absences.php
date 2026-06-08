<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'etudiant') {
  header("Location: ../login.php");
  exit();
}
require_once '../config/connexion.php';

$id = $_SESSION['id'];

$result = mysqli_query($conn, "
    SELECT a.*, m.nom_module
    FROM absence a
    JOIN module m ON a.id_module = m.id_module
    WHERE a.id_etudiant = '$id'
    ORDER BY a.date_absence DESC
");

$r2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM absence WHERE id_etudiant='$id'");
$nb_total = mysqli_fetch_assoc($r2)['total'];

$r3 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM absence WHERE id_etudiant='$id' AND justifiee=1");
$nb_justifiees = mysqli_fetch_assoc($r3)['total'];

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>EduManager — Mes absences</title>
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
          <img src="http://localhost:8000/assets/img/etudiant.png"
            style="width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0;">
          <span><?php echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']; ?></span>
        </div>
      </div>
      <div class="page">
        <div class="page-header">
          <h2><i class="fas fa-calendar-xmark"></i> Mes absences</h2>
        </div>

        <div class="stats-grid" style="margin-bottom:24px;">
          <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2;">
              <i class="fas fa-calendar-xmark" style="color:#dc2626;"></i>
            </div>
            <div class="stat-info">
              <div class="nombre" style="color:#dc2626;"><?php echo $nb_total; ?></div>
              <div class="label">Total absences</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon" style="background:#ecfdf5;">
              <i class="fas fa-check" style="color:#059669;"></i>
            </div>
            <div class="stat-info">
              <div class="nombre" style="color:#059669;"><?php echo $nb_justifiees; ?></div>
              <div class="label">Justifiées</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon" style="background:#fffbeb;">
              <i class="fas fa-xmark" style="color:#d97706;"></i>
            </div>
            <div class="stat-info">
              <div class="nombre" style="color:#d97706;"><?php echo $nb_total - $nb_justifiees; ?></div>
              <div class="label">Non justifiées</div>
            </div>
          </div>
        </div>

        <div class="carte">
          <table>
            <tr>
              <th>Module</th>
              <th>Date</th>
              <th>Justifiée</th>
              <th>Motif</th>
            </tr>
            <?php while ($a = mysqli_fetch_assoc($result)): ?>
              <tr>
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