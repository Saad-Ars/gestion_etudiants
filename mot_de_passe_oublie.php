<?php
$message = "";
$erreur  = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/connexion.php';

    $email = trim($_POST['email']);
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $confirmation = $_POST['confirmation'];

    if ($nouveau_mdp !== $confirmation) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($nouveau_mdp) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        $mdp_chiffre = MD5($nouveau_mdp);
        $trouve = false;

        // Chercher dans admin
        $r = mysqli_query($conn, "SELECT * FROM administrateur WHERE email='$email'");
        if (mysqli_num_rows($r) == 1) {
            mysqli_query($conn, "UPDATE administrateur SET mot_de_passe='$mdp_chiffre' WHERE email='$email'");
            $trouve = true;
        }

        // Chercher dans professeur
        $r = mysqli_query($conn, "SELECT * FROM professeur WHERE email='$email'");
        if (mysqli_num_rows($r) == 1) {
            mysqli_query($conn, "UPDATE professeur SET mot_de_passe='$mdp_chiffre' WHERE email='$email'");
            $trouve = true;
        }

        // Chercher dans etudiant
        $r = mysqli_query($conn, "SELECT * FROM etudiant WHERE email='$email'");
        if (mysqli_num_rows($r) == 1) {
            mysqli_query($conn, "UPDATE etudiant SET mot_de_passe='$mdp_chiffre' WHERE email='$email'");
            $trouve = true;
        }

        if ($trouve) {
            $message = "Mot de passe modifié avec succès !";
        } else {
            $erreur = "Aucun compte trouvé avec cet email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>GestEtud — Mot de passe oublié</title>
    <link rel="stylesheet" href="http://localhost:8000/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div id="spinner"><div class="spinner-cercle"></div></div>

<div class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="http://localhost:8000/assets/img/bts.png"
                 style="height:80px; width:auto; object-fit:contain; margin-bottom:12px;">
            <div class="app-sub">Réinitialisation du mot de passe</div>
        </div>

        <?php if($message != ""): ?>
            <div class="alerte-succes"><i class="fas fa-check"></i> <?php echo $message; ?></div>
            <div style="text-align:center; margin-top:10px;">
                <a href="login.php" class="btn btn-primaire"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
            </div>
        <?php else: ?>

        <?php if($erreur != ""): ?>
            <div class="alerte-erreur"><i class="fas fa-xmark"></i> <?php echo $erreur; ?></div>
        <?php endif; ?>

        <form method="POST" action="mot_de_passe_oublie.php">
            <div class="form-groupe">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" placeholder="votre@email.ma" required>
            </div>
            <div class="form-groupe">
                <label><i class="fas fa-lock"></i> Nouveau mot de passe</label>
                <input type="password" name="nouveau_mdp" placeholder="••••••••" required>
            </div>
            <div class="form-groupe">
                <label><i class="fas fa-lock"></i> Confirmer le mot de passe</label>
                <input type="password" name="confirmation" placeholder="••••••••" required>
            </div>
            <button type="submit"><i class="fas fa-rotate"></i> Réinitialiser</button>
        </form>

        <div style="text-align:center; margin-top:16px;">
            <a href="login.php" style="color:#2563eb; font-size:13px;">
                <i class="fas fa-arrow-left"></i> Retour à la connexion
            </a>
        </div>

        <?php endif; ?>
    </div>
</div>

<script>
window.addEventListener('load', function() {
    document.getElementById('spinner').style.display = 'none';
});
</script>
</body>
</html>