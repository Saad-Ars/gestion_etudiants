<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'etudiant') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$id = $_SESSION['id'];

// Stats de base
$r1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM note WHERE id_etudiant='$id'");
$nb_notes = mysqli_fetch_assoc($r1)['total'];

$r2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM absence WHERE id_etudiant='$id'");
$nb_absences = mysqli_fetch_assoc($r2)['total'];

// Moyenne générale de cet étudiant
$r3 = mysqli_query($conn, "SELECT note_controle, note_examen FROM note WHERE id_etudiant='$id'");
$total = 0;
$count = 0;
while ($n = mysqli_fetch_assoc($r3)) {
    $total += ($n['note_controle'] + $n['note_examen'] * 2) / 3;
    $count++;
}
$moyenne = $count > 0 ? round($total / $count, 2) : 0;

// Rang dans la classe
$tous = mysqli_query($conn, "SELECT id_etudiant, AVG((note_controle + note_examen * 2) / 3) AS moy FROM note GROUP BY id_etudiant ORDER BY moy DESC");
$rang = 1;
$total_etudiants = 0;
while ($row = mysqli_fetch_assoc($tous)) {
    $total_etudiants++;
    if ($row['id_etudiant'] == $id) $rang = $total_etudiants;
}

// Notes par module pour le graphique
$r4 = mysqli_query($conn, "
    SELECT m.nom_module, (n.note_controle + n.note_examen * 2) / 3 AS moy
    FROM note n
    JOIN module m ON n.id_module = m.id_module
    WHERE n.id_etudiant = '$id'
");
$labels = [];
$data = [];
while ($row = mysqli_fetch_assoc($r4)) {
    $labels[] = $row['nom_module'];
    $data[]   = round($row['moy'], 2);
}

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>GestEtud — Mon espace</title>
    <link rel="stylesheet" href="http://localhost:8000/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
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
                <h2 style="margin-bottom:24px;">Bonjour, <?php echo $_SESSION['prenom']; ?> 👋</h2>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#eff6ff;">
                            <i class="fas fa-star" style="color:#2563eb;"></i>
                        </div>
                        <div class="stat-info">
                            <div class="nombre" style="color:#2563eb;"><?php echo $nb_notes; ?></div>
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
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#ecfdf5;">
                            <i class="fas fa-chart-line" style="color:#059669;"></i>
                        </div>
                        <div class="stat-info">
                            <div class="nombre" style="color:#059669;"><?php echo $moyenne; ?>/20</div>
                            <div class="label">Moyenne générale</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#fffbeb;">
                            <i class="fas fa-trophy" style="color:#d97706;"></i>
                        </div>
                        <div class="stat-info">
                            <div class="nombre" style="color:#d97706;"><?php echo $rang; ?><sup style="font-size:14px;">e</sup></div>
                            <div class="label">Rang de la classe</div>
                        </div>
                    </div>
                </div>

                <!-- Graphique -->
                <div class="carte">
                    <h3><i class="fas fa-chart-bar" style="color:#2563eb;"></i> Évolution des notes par module</h3>
                    <div style="max-height:300px;">
                        <canvas id="graphique"></canvas>
                    </div>
                </div>

                <!-- Accès rapide -->
                <div class="carte">
                    <h3><i class="fas fa-bolt" style="color:#d97706;"></i> Accès rapide</h3>
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <a href="notes.php" class="btn btn-primaire"><i class="fas fa-star"></i> Voir mes notes</a>
                        <a href="absences.php" class="btn btn-secondaire"><i class="fas fa-calendar-xmark"></i> Voir mes absences</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const labels = <?php echo json_encode($labels); ?>;
        const data = <?php echo json_encode($data); ?>;

        const ctx = document.getElementById('graphique').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Moyenne par module',
                    data: data,
                    backgroundColor: data.map(v => v >= 10 ? 'rgba(5,150,105,0.7)' : 'rgba(220,38,38,0.7)'),
                    borderColor: data.map(v => v >= 10 ? '#059669' : '#dc2626'),
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        ticks: {
                            stepSize: 2
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y + '/20'
                        }
                    }
                }
            }
        });
    </script>
</body>
<div id="spinner">
    <div class="spinner-cercle"></div>
</div>
</html>