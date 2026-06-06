<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professeur') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$id      = $_GET['id'];
$message = "";

$result = mysqli_query($conn, "SELECT * FROM note WHERE id_note='$id'");
$n      = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controle = $_POST['note_controle'];
    $examen   = $_POST['note_examen'];
    $session  = $_POST['session'];

    $sql = "UPDATE note SET note_controle='$controle', note_examen='$examen', session='$session' WHERE id_note='$id'";

    if (mysqli_query($conn, $sql)) {
        $message = "succes";
        $result  = mysqli_query($conn, "SELECT * FROM note WHERE id_note='$id'");
        $n       = mysqli_fetch_assoc($result);
    } else {
        $message = "erreur";
    }
}

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EduManager — Modifier note</title>
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
                    <h2><i class="fas fa-pen"></i> Modifier la note</h2>
                    <a href="notes.php" class="btn btn-secondaire"><i class="fas fa-arrow-left"></i> Retour</a>
                </div>

                <?php if ($message == "succes"): ?>
                    <div class="alerte-succes"><i class="fas fa-check"></i> Note modifiée avec succès !</div>
                <?php elseif ($message == "erreur"): ?>
                    <div class="alerte-erreur"><i class="fas fa-xmark"></i> Erreur lors de la modification.</div>
                <?php endif; ?>

                <div class="carte" style="max-width:600px;">
                    <form method="POST" action="modifier_note.php?id=<?php echo $id; ?>">
                        <div class="form-groupe">
                            <label>Note de contrôle (/20)</label>
                            <input type="number" name="note_controle" value="<?php echo $n['note_controle']; ?>" min="0" max="20" step="0.25" required>
                        </div>
                        <div class="form-groupe">
                            <label>Note d'examen (/20)</label>
                            <input type="number" name="note_examen" value="<?php echo $n['note_examen']; ?>" min="0" max="20" step="0.25" required>
                        </div>
                        <div class="form-groupe">
                            <label>Session</label>
                            <select name="session">
                                <option value="Normale" <?php if ($n['session'] == 'Normale')    echo 'selected'; ?>>Normale</option>
                                <option value="Rattrapage" <?php if ($n['session'] == 'Rattrapage') echo 'selected'; ?>>Rattrapage</option>
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