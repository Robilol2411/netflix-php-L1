<?php
session_start();
require 'baseDD/database.php';

$articles = [];
$users = [];
$errorMessages = [];
$panier = [];

// Récupération du panier pour afficher le nombre d'articles
if (isset($_SESSION['user_id'])) {
    try {
        $panierQuery = "SELECT COUNT(*) as count FROM cart WHERE user_id = :user_id";
        $stmt = $conn->prepare($panierQuery);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nbItemsPanier = $result['count'];
    } catch(Exception $e) {
        $errorMessages[] = "Erreur lors de la récupération du panier: " . $e->getMessage();
        $nbItemsPanier = 0;
    }
}

// Récupération des utilisateurs
try {
    if (!isset($conn) || $conn === null) {
        throw new Exception("La connexion à la base de données n'a pas pu être établie");
    }
    
    $userQuery = "SELECT id, firstName, lastName, email FROM user ORDER BY lastName, firstName";
    $stmt = $conn->prepare($userQuery);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $errorMessages[] = "Erreur lors de la récupération des utilisateurs: " . $e->getMessage();
}

// Récupération des films en vitrine
try {
    $filmsQuery = "SELECT id, title, description, poster_path, price FROM movies ORDER BY id DESC LIMIT 6";
    $stmt = $conn->prepare($filmsQuery);
    $stmt->execute();
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $errorMessages[] = "Erreur lors de la récupération des films: " . $e->getMessage();
    $films = [];
}

// Ajout d'un film au panier
if (isset($_POST['add_to_cart']) && isset($_POST['movie_id']) && isset($_SESSION['user_id'])) {
    try {
        $movieId = $_POST['movie_id'];
        
        // Vérifier si le film est déjà dans le panier
        $checkQuery = "SELECT id, quantity FROM cart WHERE user_id = :user_id AND movie_id = :movie_id";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':movie_id', $movieId);
        $stmt->execute();
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingItem) {
            // Mise à jour de la quantité
            $newQuantity = $existingItem['quantity'] + 1;
            $updateQuery = "UPDATE cart SET quantity = :quantity WHERE id = :id";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bindParam(':quantity', $newQuantity);
            $stmt->bindParam(':id', $existingItem['id']);
            $stmt->execute();
        } else {
            // Ajouter un nouvel élément au panier
            $insertQuery = "INSERT INTO cart (user_id, movie_id, quantity) VALUES (:user_id, :movie_id, 1)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->bindParam(':movie_id', $movieId);
            $stmt->execute();
        }
        
        // Rediriger pour éviter la soumission multiple du formulaire
        header('Location: index.php?added=1');
        exit;
    } catch(Exception $e) {
        $errorMessages[] = "Erreur lors de l'ajout au panier: " . $e->getMessage();
    }
}

try {
    $filmsQuery = "SELECT id, title, description, poster_path, price FROM movies ORDER BY release_date DESC LIMIT 8";
    $stmt = $conn->prepare($filmsQuery);
    $stmt->execute();
    $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $errorMessages[] = "Erreur lors de la récupération des films: " . $e->getMessage();
    $films = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location de films - LoueTonFilm.com</title>
    <link href="assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">CINEMAX</a>
        <div class="search-container">
            <form action="recherche.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Rechercher...">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <button type="submit" id="search-button">Rechercher</button>
            </form>
        </div>
        <div class="nav-links">
            <?php 
            if (isset($_SESSION['user_id'])) {
                echo '<a href="utilisateur/user.php">Profil</a>';
                echo '<a href="utilisateur/panier.php" class="image-swap-container"><div class="image-swap-container">
                            <img class="default" src="assets/photo/shop2.png" alt="Boutique">
                            <img class="hover" src="assets/photo/shop.png" alt="Boutique survolée">
                    </div></a>';
            } 
            
            if (!isset($_SESSION['user_id'])) {
                echo '<a href="login/login.php">Se connecter</a>';
                echo '<a href="login/register.php">Nouveau compte</a>';
            } else {
                echo '<a href="login/logout.php">Se déconnecter</a>';
            }
            ?>
        </div>
    </header>

    <main>
        <!-- Message d'ajout au panier réussi -->
        <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
            <div class="alert alert-success">Le film a été ajouté à votre panier avec succès !</div>
        <?php endif; ?>
        
        <!-- Affichage des erreurs -->
        <?php if (!empty($errorMessages)): ?>
            <?php foreach ($errorMessages as $error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <h1>Bienvenue sur CINEMAX</h1>
        <h2>Revendeur officiel des meilleurs films du moment</h2>
        
        <!-- Vitrine des films -->
        <h2 class="section-title">Découvrez notre sélection</h2>
        
        <?php if (!empty($films)): ?>
            <div class="movie-grid">
    <?php foreach ($films as $film): ?>
        <div class="movie-card">
            <a href="movies.php?id=<?php echo $film['id']; ?>">
                <img src="<?php echo htmlspecialchars($film['poster_path']); ?>" alt="<?php echo htmlspecialchars($film['title']); ?>" class="movie-poster">
            </a>
            <div class="movie-info">
                <a href="movies.php?id=<?php echo $film['id']; ?>" class="movie-title">
                    <?php echo htmlspecialchars($film['title']); ?>
                </a>
                <div class="movie-price"><?php echo htmlspecialchars(number_format($film['price'], 2)); ?> €</div>
                <p class="movie-desc"><?php echo htmlspecialchars(substr($film['description'], 0, 50)) . '...'; ?></p>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" class="add-to-cart-form">
                        <input type="hidden" name="movie_id" value="<?php echo $film['id']; ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart-btn">Ajouter au panier</button>
                    </form>
                <?php else: ?>
                    <a href="login/login.php" class="login-to-buy">Connectez-vous pour acheter</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

        <?php else: ?>
            <p class="no-content">Aucun film n'est disponible pour le moment.</p>
        <?php endif; ?>
    </main>
</body>
</html>