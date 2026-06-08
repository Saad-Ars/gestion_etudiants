<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professeur') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$message   = "";
$etudiants = mysqli_query($conn, "SELECT * FROM etudiant");
$modules   = mysqli_query($conn, "SELECT * FROM module");
$annees    = mysqli_query($conn, "SELECT * FROM annee_academique");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_etudiant = $_POST['id_etudiant'];
    $id_module   = $_POST['id_module'];
    $id_annee    = $_POST['id_annee'];
    $controle    = $_POST['note_controle'];
    $examen      = $_POST['note_examen'];
    $session     = $_POST['session'];
    $date_saisie = date('Y-m-d');

    $sql = "INSERT INTO note (note_controle, note_examen, date_saisie, session, id_etudiant, id_module, id_annee)
            VALUES ('$controle','$examen','$date_saisie','$session','$id_etudiant','$id_module','$id_annee')";

    if (mysqli_query($conn, $sql)) $message = "succes";
    else $message = "erreur";
}

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EduManager — Ajouter note</title>
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
                    <h2><i class="fas fa-plus"></i> Ajouter une note</h2>
                    <a href="notes.php" class="btn btn-secondaire"><i class="fas fa-arrow-left"></i> Retour</a>
                </div>

                <?php if ($message == "succes"): ?>
                    <div class="alerte-succes"><i class="fas fa-check"></i> Note ajoutée avec succès !</div>
                <?php elseif ($message == "erreur"): ?>
                    <div class="alerte-erreur"><i class="fas fa-xmark"></i> Erreur lors de l'ajout.</div>
                <?php endif; ?>

                <div class="carte" style="max-width:600px;">
                    <form method="POST" action="ajouter_note.php">
                        <div class="form-groupe">
                            <label>Étudiant</label>
                            <select name="id_etudiant" required>
                                <option value="">-- Choisir --</option>
                                <?php while ($e = mysqli_fetch_assoc($etudiants)): ?>
                                    <option value="<?php echo $e['id_etudiant']; ?>"><?php echo $e['prenom'] . ' ' . $e['nom']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-groupe">
                            <label>Module</label>
                            <select name="id_module" required>
                                <option value="">-- Choisir --</option>
                                <?php while ($m = mysqli_fetch_assoc($modules)): ?>
                                    <option value="<?php echo $m['id_module']; ?>"><?php echo $m['nom_module']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-groupe">
                            <label>Année académique</label>
                            <select name="id_annee" required>
                                <option value="">-- Choisir --</option>
                                <?php while ($a = mysqli_fetch_assoc($annees)): ?>
                                    <option value="<?php echo $a['id_annee']; ?>"><?php echo $a['libelle']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-groupe">
                            <label>Note de contrôle (/20)</label>
                            <input type="number" name="note_controle" min="0" max="20" step="0.25" required>
                        </div>
                        <div class="form-groupe">
                            <label>Note d'examen (/20)</label>
                            <input type="number" name="note_examen" min="0" max="20" step="0.25" required>
                        </div>
                        <div class="form-groupe">
                            <label>Session</label>
                            <select name="session">
                                <option value="Normale">Normale</option>
                                <option value="Rattrapage">Rattrapage</option>
                            </select>
                        </div>
                        <div style="display:flex; gap:10px; margin-top:10px;">
                            <button type="submit" class="btn btn-succes"><i class="fas fa-save"></i> Enregistrer</button>
                            <a href="notes.php" class="btn btn-secondaire"><i class="fas fa-xmark"></i> Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<div id="spinner">
    <div class="spinner-cercle"></div>
</div>
</html>