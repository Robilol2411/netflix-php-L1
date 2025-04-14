<?php
session_start();
require '../baseDD/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$messagesErreur = [];
$messageSucces = '';
$infoUtilisateur = [];
$achats = [];

try {
    $requeteInfosUser = "SELECT id, firstName, lastName, email, created_at, description FROM user WHERE id = :user_id";
    $stmt = $conn->prepare($requeteInfosUser);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $infoUtilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($infoUtilisateur) {
        $_SESSION['firstname'] = $infoUtilisateur['firstName'];
        $_SESSION['lastname'] = $infoUtilisateur['lastName'];
        $_SESSION['email'] = $infoUtilisateur['email'];
        $_SESSION['created_at'] = $infoUtilisateur['created_at'];
        $_SESSION['description'] = $infoUtilisateur['description'];
    }
} catch (Exception $e) {
    $messagesErreur[] = "Erreur lors de la récupération des informations personnelles.";
}

try {
    $requeteAchats = "SELECT m.id, m.title, m.poster_path, p.price, p.purchase_date 
                      FROM purchases p 
                      JOIN movies m ON p.movie_id = m.id 
                      WHERE p.user_id = :user_id 
                      ORDER BY p.purchase_date DESC";
    $stmt = $conn->prepare($requeteAchats);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $achats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $messagesErreur[] = "Erreur lors de la récupération des films achetés.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $mdpActuel = $_POST['current_password'];
    $nouveauMdp = $_POST['new_password'];
    $confirmMdp = $_POST['confirm_password'];

    try {
        $requeteMdp = "SELECT password FROM user WHERE id = :user_id";
        $stmt = $conn->prepare($requeteMdp);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $donneesUtilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($donneesUtilisateur && password_verify($mdpActuel, $donneesUtilisateur['password'])) {
            if ($nouveauMdp === $confirmMdp) {
                $mdpHash = password_hash($nouveauMdp, PASSWORD_DEFAULT);
                $requeteMiseAJour = "UPDATE user SET password = :password WHERE id = :user_id";
                $stmt = $conn->prepare($requeteMiseAJour);
                $stmt->bindParam(':password', $mdpHash, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();
                $messageSucces = "Votre mot de passe a bien été modifié.";
            } else {
                $messagesErreur[] = "Les nouveaux mots de passe ne correspondent pas.";
            }
        } else {
            $messagesErreur[] = "Le mot de passe actuel est incorrect.";
        }
    } catch (Exception $e) {
        $messagesErreur[] = "Erreur lors du changement de mot de passe.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - CINEMAX</title>
    <link href="../assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="../index.php" class="logo">CINEMAX</a>
        <div class="search-container">
            <form action="../recherche.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Rechercher un film...">
                <button type="submit">Rechercher</button>
            </form>
        </div>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../utilisateur/panier.php" class="image-swap-container">
                    <img class="default" src="../assets/photo/shop2.png" alt="Boutique">
                    <img class="hover" src="../assets/photo/shop.png" alt="Boutique survolée">
                </a>
                <a href="../login/logout.php">Déconnexion</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <?php if (!empty($messagesErreur)): ?>
            <?php foreach ($messagesErreur as $erreur): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($messageSucces)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($messageSucces, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <h2 class="section-title">Mes films achetés</h2>
        <?php if (empty($achats)): ?>
            <p class="no-content">Vous n'avez pas encore acheté de films.</p>
        <?php else: ?>
            <div class="movie-grid">
                <?php foreach ($achats as $film): ?>
                    <div class="movie-card">
                        <img src="<?php echo htmlspecialchars($film['poster_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($film['title'], ENT_QUOTES, 'UTF-8'); ?>" class="movie-poster">
                        <div class="movie-info">
                            <div class="movie-title"><?php echo htmlspecialchars($film['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="movie-price"><?php echo htmlspecialchars(number_format($film['price'], 2), ENT_QUOTES, 'UTF-8'); ?> €</div>
                            <div class="movie-date">Acheté le: <?php echo htmlspecialchars(date('d/m/Y', strtotime($film['purchase_date'])), ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 class="section-title">Informations personnelles</h2>
        <div class="account-info">
            <p>
                <strong>Nom:</strong> <?php echo htmlspecialchars($_SESSION['lastname'], ENT_QUOTES, 'UTF-8'); ?><br>
                <strong>Prénom:</strong> <?php echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES, 'UTF-8'); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8'); ?><br>
                <strong>ID Client:</strong> <?php echo htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8'); ?><br>
                <strong>Inscrit le:</strong> <?php echo htmlspecialchars(date('d/m/Y à H:i', strtotime($_SESSION['created_at'])), ENT_QUOTES, 'UTF-8'); ?><br>
                <?php if (!empty($_SESSION['description'])): ?>
                    <strong>À propos de moi:</strong> <?php echo htmlspecialchars($_SESSION['description'], ENT_QUOTES, 'UTF-8'); ?><br>
                <?php endif; ?>
            </p>
            <div class="password-form">
                <h3>Changer mon mot de passe</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div></br>
                    <button type="submit" name="change_password" class="submit-btn">Mettre à jour</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');

            form.addEventListener('submit', function (event) {
                if (newPassword.value !== confirmPassword.value) {
                    event.preventDefault();
                    alert('Les mots de passe ne correspondent pas.');
                }
            });
        });
    </script>
</body>
</html>