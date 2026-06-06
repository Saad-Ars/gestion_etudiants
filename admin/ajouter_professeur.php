<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../config/connexion.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom        = $_POST['nom'];
    $prenom     = $_POST['prenom'];
    $email      = $_POST['email'];
    $mdp        = MD5($_POST['mot_de_passe']);
    $telephone  = $_POST['telephone'];
    $specialite = $_POST['specialite'];

    $sql = "INSERT INTO professeur (nom, prenom, email, mot_de_passe, telephone, specialite)
            VALUES ('$nom','$prenom','$email','$mdp','$telephone','$specialite')";

    if (mysqli_query($conn, $sql)) $message = "succes";
    else $message = "erreur";
}

$initiale = strtoupper(substr($_SESSION['prenom'], 0, 1) . substr($_SESSION['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EduManager — Ajouter professeur</title>
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
                    <h2><i class="fas fa-user-plus"></i> Ajouter un professeur</h2>
                    <a href="professeurs.php" class="btn btn-secondaire"><i class="fas fa-arrow-left"></i> Retour</a>
                </div>

                <?php if ($message == "succes"): ?>
                    <div class="alerte-succes"><i class="fas fa-check"></i> Professeur ajouté avec succès !</div>
                <?php elseif ($message == "erreur"): ?>
                    <div class="alerte-erreur"><i class="fas fa-xmark"></i> Erreur. Email déjà utilisé ?</div>
                <?php endif; ?>

                <div class="carte" style="max-width:600px;">
                    <form method="POST" action="ajouter_professeur.php">
                        <div class="form-groupe">
                            <label>Nom</label>
                            <input type="text" name="nom" required>
                        </div>
                        <div class="form-groupe">
                            <label>Prénom</label>
                            <input type="text" name="prenom" required>
                        </div>
                        <div class="form-groupe">
                            <label>Email</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-groupe">
                            <label>Mot de passe</label>
                            <input type="password" name="mot_de_passe" required>
                        </div>
                        <div class="form-groupe">
                            <label>Téléphone</label>
                            <input type="text" name="telephone">
                        </div>
                        <div class="form-groupe">
                            <label>Spécialité</label>
                            <input type="text" name="specialite">
                        </div>
                        <div style="display:flex; gap:10px; margin-top:10px;">
                            <button type="submit" class="btn btn-succes"><i class="fas fa-save"></i> Enregistrer</button>
                            <a href="professeurs.php" class="btn btn-secondaire"><i class="fas fa-xmark"></i> Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>