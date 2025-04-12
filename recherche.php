<?php
session_start();
require 'baseDD/database.php';

$searchResults = [];
$errorMessages = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $query = htmlspecialchars($_GET['q']); // Sanitize user input
    $apiKey = 'b4d10555719ced3748435bc30d8b3f7b'; // Your TMDB API key
    $url = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&language=en-US&query=" . urlencode($query);

    try {
        // Fetch movies from TMDB
        $response = file_get_contents($url);
        $movies = json_decode($response, true)['results'];

        foreach ($movies as $movie) {
            $title = $movie['title'];
            $description = $movie['overview'];
            $posterPath = !empty($movie['poster_path']) 
                ? "https://image.tmdb.org/t/p/w500" . $movie['poster_path'] 
                : "https://via.placeholder.com/500x750?text=No+Image"; // Default placeholder image
            $releaseDate = !empty($movie['release_date']) ? $movie['release_date'] : null;
            $price = rand(599, 1999) / 100; // Random price between 5.99 and 19.99

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO movies (title, description, poster_path, release_date, price, created_at, updated_at) 
                                    VALUES (:title, :description, :poster_path, :release_date, :price, NOW(), NOW())
                                    ON DUPLICATE KEY UPDATE updated_at = NOW()");
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':poster_path' => $posterPath,
                ':release_date' => $releaseDate,
                ':price' => $price,
            ]);
        }

        // Retrieve search results from the database
        $searchQuery = "SELECT id, title, description, poster_path, price FROM movies 
                        WHERE title LIKE :query OR description LIKE :query 
                        ORDER BY created_at DESC";
        $stmt = $conn->prepare($searchQuery);
        $searchTerm = '%' . $query . '%';
        $stmt->bindParam(':query', $searchTerm);
        $stmt->execute();
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $errorMessages[] = "Erreur lors de la recherche: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche - LoueTonFilm.com</title>
    <link href="assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">CINEMAX</a>
        <div class="search-container">
            <form action="recherche.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Rechercher..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <button type="submit" class="search-button">Rechercher</button>
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
        <h1>Résultats de recherche</h1>

        <!-- Affichage des erreurs -->
        <?php if (!empty($errorMessages)): ?>
            <?php foreach ($errorMessages as $error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($searchResults)): ?>
            <div class="movie-grid">
                <?php foreach ($searchResults as $movie): ?>
                    <div class="movie-card">
                        <a href="movies.php?id=<?php echo $movie['id']; ?>">
                            <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" class="movie-poster">
                        </a>
                        <div class="movie-info">
                            <a href="movies.php?id=<?php echo $movie['id']; ?>" class="movie-title">
                                <?php echo htmlspecialchars($movie['title']); ?>
                            </a>
                            <div class="movie-price"><?php echo htmlspecialchars(number_format($movie['price'], 2)); ?> €</div>
                            <p class="movie-desc"><?php echo htmlspecialchars(substr($movie['description'], 0, 50)) . '...'; ?></p>
                            
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST" class="add-to-cart-form">
                                    <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
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
            <p class="no-content">Aucun résultat trouvé pour "<?php echo htmlspecialchars($_GET['q']); ?>".</p>
        <?php endif; ?>
    </main>
</body>
</html>