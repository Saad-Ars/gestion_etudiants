<?php
session_start();

// Si déjà connecté, rediriger directement
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin')      header("Location: admin/dashboard.php");
    if ($_SESSION['role'] == 'professeur') header("Location: professeur/dashboard.php");
    if ($_SESSION['role'] == 'etudiant')   header("Location: etudiant/dashboard.php");
    exit();
}

$erreur = "";

// Traitement du formulaire quand on clique sur "Connexion"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once 'config/connexion.php';

    $email = $_POST['email'];
    $mdp   = MD5($_POST['mot_de_passe']);

    // Chercher dans la table administrateur
    $sql    = "SELECT * FROM administrateur WHERE email='$email' AND mot_de_passe='$mdp'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['role']    = 'admin';
        $_SESSION['id']      = $user['id_admin'];
        $_SESSION['nom']     = $user['nom'];
        $_SESSION['prenom']  = $user['prenom'];
        header("Location: admin/dashboard.php");
        exit();
    }

    // Chercher dans la table professeur
    $sql    = "SELECT * FROM professeur WHERE email='$email' AND mot_de_passe='$mdp'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['role']   = 'professeur';
        $_SESSION['id']     = $user['id_professeur'];
        $_SESSION['nom']    = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        header("Location: professeur/dashboard.php");
        exit();
    }

    // Chercher dans la table etudiant
    $sql    = "SELECT * FROM etudiant WHERE email='$email' AND mot_de_passe='$mdp'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['role']   = 'etudiant';
        $_SESSION['id']     = $user['id_etudiant'];
        $_SESSION['nom']    = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        header("Location: etudiant/dashboard.php");
        exit();
    }

    // Aucun résultat trouvé
    $erreur = "Email ou mot de passe incorrect.";
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>EduManager — Connexion</title>
    <link rel="stylesheet" href="http://localhost:8000/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="login-page">
        <div class="login-box">
            <div class="login-logo">
    <img src="http://localhost:8000/assets/img/bts.png" 
         style="height:100px; width:auto; object-fit:contain; margin-bottom:12px;">
    <div class="app-sub">Système de gestion des étudiants Saad-Younes</div>
</div>
            <h2>Connectez-vous</h2>

            <?php if ($erreur != ""): ?>
                <div class="alerte-erreur"><i class="fas fa-circle-exclamation"></i> <?php echo $erreur; ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-groupe">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="marhba@bik.ma" required>
                </div>
                <div class="form-groupe">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" name="mot_de_passe" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>

         
            </form>
        </div>
    </div>
</body>       <footer class="page-footer">
    Réalisé par : <strong>Saad et Younes</strong><br>
    Encadré par : <strong>M. El Merrouchi</strong>
</footer>

</html>