<?php
session_start();
require 'baseDD/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Film non trouvé.');
}

$movieId = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = :id");
    $stmt->bindParam(':id', $movieId, PDO::PARAM_INT);
    $stmt->execute();
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        die('Film introuvable.');
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - CINEMAX</title>
    <link href="assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">CINEMAX</a>
        <div class="search-container">
            <form action="recherche.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Rechercher...">
                <button type="submit">Rechercher</button>
            </form>
        </div>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="utilisateur/user.php">Profil</a>
                <a href="utilisateur/panier.php" class="image-swap-container">
                    <img class="default" src="assets/photo/shop2.png" alt="Boutique">
                    <img class="hover" src="assets/photo/shop.png" alt="Boutique survolée">
                </a>
                <a href="login/logout.php">Se déconnecter</a>
            <?php else: ?>
                <a href="login/login.php">Se connecter</a>
                <a href="login/register.php">Nouveau compte</a>
            <?php endif; ?>
        </div>
    </header>
    <main>
        <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
        <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
        <p><?php echo htmlspecialchars($movie['description']); ?></p>
        <p>Prix : <?php echo htmlspecialchars(number_format($movie['price'], 2)); ?> €</p>
    </main>
</body>
</html>