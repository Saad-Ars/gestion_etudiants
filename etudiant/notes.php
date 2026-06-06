<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'etudiant') {
  header("Location: ../login.php");
  exit();
}
require_once '../config/connexion.php';

$id = $_SESSION['id'];

$result = mysqli_query($conn, "
    SELECT n.*, m.nom_module, m.coefficient, a.libelle AS annee
    FROM note n
    JOIN module m ON n.id_module = m.id_module
    JOIN annee_academique a ON n.id_annee = a.id_annee
    WHERE n.id_etudiant = '$id'
    ORDER BY m.nom_module
");

$r2 = mysqli_query($conn, "SELECT note_controle, note_examen FROM note WHERE id_etudiant='$id'");
$total = 0;
$count = 0;
while ($n = mysqli_fetch_assoc($r2)) {
  $total += ($n['note_controle'] + $n['note_examen'] * 2) / 3;
  $count++;
}
$moyenne = $count > 0 ? number_format($total / $count, 2) : "—";

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>EduManager — Mes notes</title>
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
          <img src="http://localhost:8000/assets/img/admin.png"
            style="width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0;">
          <span><?php echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']; ?></span>
        </div>
      </div>
      <div class="page">
        <div class="page-header">
          <h2><i class="fas fa-star"></i> Mes notes</h2>
          <a href="releve_pdf.php" class="btn btn-primaire">
            <i class="fas fa-file-pdf"></i> Télécharger le relevé PDF
          </a>
        </div>

        <div class="stat-card" style="margin-bottom:24px; max-width:300px;">
          <div class="stat-icon" style="background:#ecfdf5;">
            <i class="fas fa-chart-line" style="color:#059669;"></i>
          </div>
          <div class="stat-info">
            <div class="nombre" style="color:#059669;"><?php echo $moyenne; ?>/20</div>
            <div class="label">Moyenne générale</div>
          </div>
        </div>

        <div class="carte">
          <table>
            <tr>
              <th>Module</th>
              <th>Coefficient</th>
              <th>Contrôle</th>
              <th>Examen</th>
              <th>Moyenne</th>
              <th>Session</th>
              <th>Résultat</th>
            </tr>
            <?php while ($n = mysqli_fetch_assoc($result)):
              $moy = ($n['note_controle'] + $n['note_examen'] * 2) / 3;
            ?>
              <tr>
                <td><?php echo $n['nom_module']; ?></td>
                <td><?php echo $n['coefficient']; ?></td>
                <td><?php echo $n['note_controle']; ?>/20</td>
                <td><?php echo $n['note_examen']; ?>/20</td>
                <td style="font-weight:600; color:<?php echo $moy >= 10 ? '#059669' : '#dc2626'; ?>">
                  <?php echo number_format($moy, 2); ?>/20
                </td>
                <td><?php echo $n['session']; ?></td>
                <td>
                  <?php if ($moy >= 10): ?>
                    <span class="badge-ok">Validé</span>
                  <?php else: ?>
                    <span class="badge-nok">Non validé</span>
                  <?php endif; ?>
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