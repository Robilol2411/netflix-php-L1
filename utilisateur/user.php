<?php
session_start();
require '../baseDD/database.php';

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$films = [];
$utilisateurs = [];
$messagesErreur = [];
$messageSucces = '';
$infoUtilisateur = [];

// Récupération des informations personnelles de l'utilisateur
try {
    if (!isset($conn) || $conn === null) {
        throw new Exception("La connexion à la base de données n'a pas pu être établie");
    }
    
    // Récupérer les informations de l'utilisateur connecté
    $requeteInfosUser = "SELECT id, firstName, lastName, email, created_at, description 
                         FROM user 
                         WHERE id = :user_id";
    $stmt = $conn->prepare($requeteInfosUser);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $infoUtilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Mettre à jour les données de session si nécessaire
    if ($infoUtilisateur) {
        $_SESSION['firstname'] = $infoUtilisateur['firstName'];
        $_SESSION['lastname'] = $infoUtilisateur['lastName'];
        $_SESSION['email'] = $infoUtilisateur['email'];
        $_SESSION['created_at'] = $infoUtilisateur['created_at'];
        $_SESSION['description'] = $infoUtilisateur['description'];
    }
} catch(Exception $e) {
    $messagesErreur[] = "Erreur lors de la récupération des informations personnelles: " . $e->getMessage();
}

// Récupération des films achetés par l'utilisateur
try {
    if (!isset($conn) || $conn === null) {
        throw new Exception("La connexion à la base de données n'a pas pu être établie");
    }
    
    // Requête pour obtenir les films achetés par l'utilisateur
    $requeteAchats = "SELECT m.id, m.title, m.poster_path, p.price, p.purchase_date 
                      FROM purchases p 
                      JOIN movies m ON p.movie_id = m.id 
                      WHERE p.user_id = :user_id 
                      ORDER BY p.purchase_date DESC";
    $stmt = $conn->prepare($requeteAchats);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $achats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $messagesErreur[] = "Erreur lors de la récupération des films achetés: " . $e->getMessage();
}

// Gestion du changement de mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $mdpActuel = $_POST['current_password'];
    $nouveauMdp = $_POST['new_password'];
    $confirmMdp = $_POST['confirm_password'];
    
    try {
        // Vérification du mot de passe actuel
        $requeteMdp = "SELECT password FROM user WHERE id = :user_id";
        $stmt = $conn->prepare($requeteMdp);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $donneesUtilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($donneesUtilisateur && password_verify($mdpActuel, $donneesUtilisateur['password'])) {
            // Vérification que les nouveaux mots de passe correspondent
            if ($nouveauMdp === $confirmMdp) {
                // Mise à jour du mot de passe
                $mdpHash = password_hash($nouveauMdp, PASSWORD_DEFAULT);
                $requeteMiseAJour = "UPDATE user SET password = :password WHERE id = :user_id";
                $stmt = $conn->prepare($requeteMiseAJour);
                $stmt->bindParam(':password', $mdpHash);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                
                $messageSucces = "Votre mot de passe a bien été modifié !";
            } else {
                $messagesErreur[] = "Les nouveaux mots de passe ne correspondent pas.";
            }
        } else {
            $messagesErreur[] = "Le mot de passe actuel n'est pas correct.";
        }
    } catch(Exception $e) {
        $messagesErreur[] = "Erreur lors du changement de mot de passe: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - LoueTonFilm.com</title>
    <link href="../assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="../index.php" class="logo">LoueTonFilm.com</a>
        <div class="search-container">
            <form action="../recherche.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Rechercher un film...">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <button type="submit">Rechercher</button>
            </form>
        </div>
        <div class="nav-links">
            <?php 
            if (isset($_SESSION['user_id'])) {
                echo '<a href="../articles/create_article.php" class="image-swap-container"><div class="image-swap-container">
                            <img class="default" src="../assets/photo/shop2.png" alt="Boutique">
                            <img class="hover" src="../assets/photo/shop.png" alt="Boutique survolée">
                    </div></a>';
            } 
            ?>
            <a href="../login/logout.php">Déconnexion</a>
        </div>
    </header>

    <main>
        <!-- Affichage des messages d'erreur -->
        <?php if (!empty($messagesErreur)): ?>
            <?php foreach ($messagesErreur as $erreur): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erreur); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Affichage du message de succès -->
        <?php if (!empty($messageSucces)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($messageSucces); ?></div>
        <?php endif; ?>
        
        <!-- SECTION 1: Films achetés -->
        <h2 class="section-title">Mes films achetés</h2>
        <?php if (empty($achats)): ?>
            <p class="no-content">Vous n'avez pas encore acheté de films. Parcourez notre catalogue pour trouver votre bonheur !</p>
        <?php else: ?>
            <div class="movie-grid">
                <?php foreach ($achats as $film): ?>
                    <div class="movie-card">
                        <img src="<?php echo htmlspecialchars($film['poster_path']); ?>" alt="<?php echo htmlspecialchars($film['title']); ?>" class="movie-poster">
                        <div class="movie-info">
                            <div class="movie-title"><?php echo htmlspecialchars($film['title']); ?></div>
                            <div class="movie-price"><?php echo htmlspecialchars(number_format($film['price'], 2)); ?></div>
                            <div class="movie-date">Acheté le: <?php echo htmlspecialchars(date('d/m/Y', strtotime($film['purchase_date']))); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- SECTION 2: Informations du compte -->
        <h2 class="section-title">Informations personnelles</h2>
        <div class="account-info">
            <p>
                <strong>Nom:</strong> <?php echo htmlspecialchars($_SESSION['lastname']); ?><br>
                <strong>Prénom:</strong> <?php echo htmlspecialchars($_SESSION['firstname']); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?><br>
                <strong>ID Client:</strong> <?php echo htmlspecialchars($_SESSION['user_id']); ?><br>
                <strong>Inscrit le:</strong> <?php echo htmlspecialchars(date('d/m/Y à H:i', strtotime($_SESSION['created_at']))); ?><br>
                <?php if(isset($_SESSION['description']) && !empty($_SESSION['description'])): ?>
                <strong>À propos de moi:</strong> <?php echo htmlspecialchars($_SESSION['description']); ?><br>
                <?php endif; ?>
            </p>
            
            <!-- Formulaire de changement de mot de passe -->
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
                    </div>
                    <button type="submit" name="change_password" class="submit-btn">Mettre à jour</button>
                </form>
            </div>
        </div>
    </main>

    <script>
    // Vérification des mots de passe
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        
        form.addEventListener('submit', function(event) {
            if (newPassword.value !== confirmPassword.value) {
                event.preventDefault();
                alert('Les mots de passe ne correspondent pas !');
            }
        });
    });
    </script>
</body>
</html>