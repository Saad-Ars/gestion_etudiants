<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$r1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM etudiant");
$nb_etudiants = mysqli_fetch_assoc($r1)['total'];

$r2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM professeur");
$nb_professeurs = mysqli_fetch_assoc($r2)['total'];

$r3 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM module");
$nb_modules = mysqli_fetch_assoc($r3)['total'];

$r4 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM filiere");
$nb_filieres = mysqli_fetch_assoc($r4)['total'];

// Moyennes par filière
$r5 = mysqli_query($conn, "
    SELECT f.nom_filiere, 
           AVG((n.note_controle + n.note_examen * 2) / 3) AS moy
    FROM note n
    JOIN etudiant e ON n.id_etudiant = e.id_etudiant
    JOIN filiere f ON e.id_filiere = f.id_filiere
    GROUP BY f.id_filiere
");
$filieres_labels = [];
$filieres_moy = [];
while ($row = mysqli_fetch_assoc($r5)) {
    $filieres_labels[] = $row['nom_filiere'];
    $filieres_moy[]    = round($row['moy'], 2);
}

// Absences par mois
$r6 = mysqli_query($conn, "
    SELECT MONTH(date_absence) AS mois, COUNT(*) AS total
    FROM absence
    GROUP BY MONTH(date_absence)
    ORDER BY mois
");
$mois_labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
$absences_data = array_fill(0, 12, 0);
while ($row = mysqli_fetch_assoc($r6)) {
    $absences_data[$row['mois'] - 1] = $row['total'];
}

// Top 5 étudiants
$r7 = mysqli_query($conn, "
    SELECT e.nom, e.prenom,
           AVG((n.note_controle + n.note_examen * 2) / 3) AS moy
    FROM note n
    JOIN etudiant e ON n.id_etudiant = e.id_etudiant
    GROUP BY n.id_etudiant
    ORDER BY moy DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>GestEtud — Admin</title>
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
</script>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="http://localhost:8000/assets/img/bts.png"
                    style="height:45px; width:auto; object-fit:contain; margin-bottom:4px;">
                <div class="app-sub">Espace Administrateur</div>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="actif"><i class="fas fa-house"></i> Tableau de bord</a>
                <a href="etudiants.php"><i class="fas fa-user-graduate"></i> Étudiants</a>
                <a href="professeurs.php"><i class="fas fa-chalkboard-user"></i> Professeurs</a>
                <a href="modules.php"><i class="fas fa-book"></i> Modules</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php"><i class="fas fa-right-from-bracket"></i> Déconnexion</a>
            </div>
        </aside>

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
                <!-- Stats -->
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
                            <i class="fas fa-chalkboard-user" style="color:#059669;"></i>
                        </div>
                        <div class="stat-info">
                            <div class="nombre" style="color:#059669;"><?php echo $nb_professeurs; ?></div>
                            <div class="label">Professeurs</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#fffbeb;">
                            <i class="fas fa-book" style="color:#d97706;"></i>
                        </div>
                        <div class="stat-info">
                            <div class="nombre" style="color:#d97706;"><?php echo $nb_modules; ?></div>
                            <div class="label">Modules</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#fef2f2;">
                            <i class="fas fa-layer-group" style="color:#dc2626;"></i>
                        </div>
                        <div class="stat-info">
                            <div class="nombre" style="color:#dc2626;"><?php echo $nb_filieres; ?></div>
                            <div class="label">Filières</div>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">

                    <!-- Moyennes par filière -->
                    <div class="carte">
                        <h3><i class="fas fa-chart-bar" style="color:#2563eb;"></i> Moyennes par filière</h3>
                        <canvas id="graphFilieres" height="200"></canvas>
                    </div>

                    <!-- Absences par mois -->
                    <div class="carte">
                        <h3><i class="fas fa-chart-line" style="color:#dc2626;"></i> Absences par mois</h3>
                        <canvas id="graphAbsences" height="200"></canvas>
                    </div>

                </div>

                <!-- Top 5 étudiants -->
                <div class="carte">
                    <h3><i class="fas fa-trophy" style="color:#d97706;"></i> Top 5 meilleurs étudiants</h3>
                    <table>
                        <tr>
                            <th>Rang</th>
                            <th>Étudiant</th>
                            <th>Moyenne</th>
                            <th>Résultat</th>
                        </tr>
                        <?php $rang = 1;
                        while ($e = mysqli_fetch_assoc($r7)): ?>
                            <tr>
                                <td>
                                    <?php if ($rang == 1): ?>
                                        <i class="fas fa-trophy" style="color:#d97706;"></i>
                                    <?php elseif ($rang == 2): ?>
                                        <i class="fas fa-trophy" style="color:#94a3b8;"></i>
                                    <?php elseif ($rang == 3): ?>
                                        <i class="fas fa-trophy" style="color:#b45309;"></i>
                                    <?php else: ?>
                                        <?php echo $rang; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $e['prenom'] . ' ' . $e['nom']; ?></td>
                                <td style="font-weight:600; color:<?php echo $e['moy'] >= 10 ? '#059669' : '#dc2626'; ?>">
                                    <?php echo number_format($e['moy'], 2); ?>/20
                                </td>
                                <td>
                                    <?php if ($e['moy'] >= 10): ?>
                                        <span class="badge-ok">Validé</span>
                                    <?php else: ?>
                                        <span class="badge-nok">Non validé</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php $rang++;
                        endwhile; ?>
                    </table>
                </div>

                <!-- Accès rapide -->
                <div class="carte">
                    <h3><i class="fas fa-bolt" style="color:#d97706;"></i> Accès rapide</h3>
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <a href="etudiants.php" class="btn btn-primaire"><i class="fas fa-user-graduate"></i> Gérer les étudiants</a>
                        <a href="professeurs.php" class="btn btn-succes"><i class="fas fa-chalkboard-user"></i> Gérer les professeurs</a>
                        <a href="modules.php" class="btn btn-warning"><i class="fas fa-book"></i> Gérer les modules</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Graphique moyennes par filière
        new Chart(document.getElementById('graphFilieres'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($filieres_labels); ?>,
                datasets: [{
                    label: 'Moyenne',
                    data: <?php echo json_encode($filieres_moy); ?>,
                    backgroundColor: 'rgba(37,99,235,0.7)',
                    borderColor: '#2563eb',
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Graphique absences par mois
        new Chart(document.getElementById('graphAbsences'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($mois_labels); ?>,
                datasets: [{
                    label: 'Absences',
                    data: <?php echo json_encode($absences_data); ?>,
                    backgroundColor: 'rgba(220,38,38,0.1)',
                    borderColor: '#dc2626',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>

</html>