<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$login_success = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : false;
unset($_SESSION['login_success']); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles - Mon Projet PHP</title>
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php if ($login_success): ?>
            <div class="success-message">
                Connexion réussie! Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </div>
        <?php endif; ?>

        <h1>Mes Articles</h1>
        
        <div class="user-info">
            <p>Connecté en tant que: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <a href="../create/logout.php" class="logout-button">Déconnexion</a>
        </div>
        <div class="articles-list">
            <p>Aucun article pour le moment.</p>
        </div>
    </div>
</body>
</html>