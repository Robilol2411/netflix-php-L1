<?php
session_start();
require '../baseDD/database.php';

$errorMessages = [];

if (isset($_POST['add_to_cart']) && isset($_POST['movie_id'])) {
    $movieId = filter_var($_POST['movie_id'], FILTER_VALIDATE_INT);
    if (!$movieId) {
        die('Invalid movie ID.');
    }

    try {
        $checkQuery = "SELECT id, quantity FROM cart WHERE user_id = :user_id AND movie_id = :movie_id";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
        $stmt->execute();
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + 1;
            $updateQuery = "UPDATE cart SET quantity = :quantity WHERE id = :id";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
            $stmt->bindParam(':id', $existingItem['id'], PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $insertQuery = "INSERT INTO cart (user_id, movie_id, quantity) VALUES (:user_id, :movie_id, 1)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
            $stmt->execute();
        }
        header('Location: action.php?added=1');
        exit;
    } catch (Exception $e) {
        error_log($e->getMessage());
        $errorMessages[] = "Une erreur est survenue. Veuillez réessayer.";
    }
}

try {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE category = :category ORDER BY release_date DESC LIMIT 20");
    $stmt->bindValue(':category', 'horror', PDO::PARAM_STR);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Films d'Horreur - CINEMAX</title>
    <link href="../assets/style_index.css" rel="stylesheet">
</head>
<body>
<header class="navbar">
    <a href="../index.php" class="logo">CINEMAX</a>
    <div class="search-container">
        <form action="../recherche.php" method="GET">
            <input type="text" name="q" class="search-input" placeholder="Rechercher...">
            <button type="submit" id="search-button">Rechercher</button>
        </form>
    </div>
    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../utilisateur/user.php">Profil</a>
            <a href="../utilisateur/panier.php" class="image-swap-container">
                <img class="default" src="../assets/photo/shop2.png" alt="Boutique">
                <img class="hover" src="../assets/photo/shop.png" alt="Boutique survolée">
            </a>
            <a href="../login/logout.php">Se déconnecter</a>
        <?php else: ?>
            <a href="../login/login.php">Se connecter</a>
            <a href="../login/register.php">Nouveau compte</a>
        <?php endif; ?>
    </div>
</header>
<main>
    <h1>Films d'Horreur</h1>
    <?php if (!empty($errorMessages)): ?>
        <?php foreach ($errorMessages as $error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (!empty($movies)): ?>
        <div class="movie-grid">
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <a href="../movies.php?id=<?php echo htmlspecialchars($movie['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <img src="<?php echo htmlspecialchars($movie['poster_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8'); ?>" class="movie-poster">
                    </a>
                    <div class="movie-info">
                        <a href="../movies.php?id=<?php echo htmlspecialchars($movie['id'], ENT_QUOTES, 'UTF-8'); ?>" class="movie-title"><?php echo htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8'); ?></a>
                        <div class="movie-price"><?php echo htmlspecialchars(number_format($movie['price'], 2), ENT_QUOTES, 'UTF-8'); ?> </div>
                        <p class="movie-desc"><?php echo htmlspecialchars(substr($movie['description'], 0, 100), ENT_QUOTES, 'UTF-8'); ?>...</p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="movie_id" value="<?php echo htmlspecialchars($movie['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart-btn">Ajouter au panier</button>
                            </form>
                        <?php else: ?>
                            <a href="../login/login.php" class="login-to-buy">Connectez-vous pour acheter</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-content">Aucun film disponible dans cette catégorie.</p>
    <?php endif; ?>
</main>
</body>
</html>