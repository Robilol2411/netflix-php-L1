<?php
session_start();
require 'baseDD/database.php';

$users = [];
$errorMessages = [];


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
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Film non trouvé.');
}

$movieId = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = :id");
    $stmt->bindParam(':id', $movieId);
    $stmt->execute();
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        die('Film introuvable.');
    }
} catch(Exception $e) {
    die("Erreur : " . $e->getMessage());
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
                <button type="submit">Rechercher</button>
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

    <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
    <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
    <p><?php echo htmlspecialchars($movie['description']); ?></p>
    <p>Prix : <?php echo htmlspecialchars(number_format($movie['price'], 2)); ?> €</p>
</body>
</html>
