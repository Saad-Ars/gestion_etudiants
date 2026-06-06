<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$message  = "";
$filieres = mysqli_query($conn, "SELECT * FROM filiere");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom        = $_POST['nom_module'];
    $coef       = $_POST['coefficient'];
    $heures     = $_POST['nb_heures'];
    $semestre   = $_POST['semestre'];
    $id_filiere = $_POST['id_filiere'];

    $sql = "INSERT INTO module (nom_module, coefficient, nb_heures, semestre, id_filiere)
            VALUES ('$nom','$coef','$heures','$semestre','$id_filiere')";

    if (mysqli_query($conn, $sql)) $message = "succes";
    else $message = "erreur";
}

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EduManager — Ajouter module</title>
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
        </div>
        <div class="page">
            <div class="page-header">
                <h2><i class="fas fa-book-medical"></i> Ajouter un module</h2>
                <a href="modules.php" class="btn btn-secondaire"><i class="fas fa-arrow-left"></i> Retour</a>
            </div>

            <?php if ($message == "succes"): ?>
                <div class="alerte-succes"><i class="fas fa-check"></i> Module ajouté avec succès !</div>
            <?php elseif ($message == "erreur"): ?>
                <div class="alerte-erreur"><i class="fas fa-xmark"></i> Erreur lors de l'ajout.</div>
            <?php endif; ?>

            <div class="carte" style="max-width:600px;">
                <form method="POST" action="ajouter_module.php">
                    <div class="form-groupe">
                        <label>Nom du module</label>
                        <input type="text" name="nom_module" required>
                    </div>
                    <div class="form-groupe">
                        <label>Coefficient</label>
                        <input type="number" name="coefficient" value="1" min="1">
                    </div>
                    <div class="form-groupe">
                        <label>Nombre d'heures</label>
                        <input type="number" name="nb_heures" value="30">
                    </div>
                    <div class="form-groupe">
                        <label>Semestre</label>
                        <select name="semestre">
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                            <option value="S4">S4</option>
                        </select>
                    </div>
                    <div class="form-groupe">
                        <label>Filière</label>
                        <select name="id_filiere" required>
                            <option value="">-- Choisir --</option>
                            <?php while ($f = mysqli_fetch_assoc($filieres)): ?>
                                <option value="<?php echo $f['id_filiere']; ?>"><?php echo $f['nom_filiere']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div style="display:flex; gap:10px; margin-top:10px;">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Enregistrer</button>
                        <a href="modules.php" class="btn btn-secondaire"><i class="fas fa-xmark"></i> Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</body>

</html>