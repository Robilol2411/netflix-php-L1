<?php
session_start();
require 'baseDD/database.php';
require 'baseDD/envapi.php';

if (!isset($_SESSION['user_id'])) {
    die('Vous devez être connecté pour effectuer cette action.');
}

$searchResults = [];
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
        header('Location: recherche.php?q=' . urlencode($_GET['q']) . '&added=1');
        exit;
    } catch (Exception $e) {
        error_log($e->getMessage());
        $errorMessages[] = "Une erreur est survenue. Veuillez réessayer.";
    }
}

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $query = htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8');
    $apiKey = $TMDB_API_KEY;
    if (!$apiKey) {
        die('API key not configured.');
    }
    $url = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&language=en-US&query=" . urlencode($query);

    try {
        $response = file_get_contents($url);
        $movies = json_decode($response, true)['results'] ?? [];

        if (empty($movies)) {
            $errorMessages[] = "Aucun film trouvé pour votre recherche.";
        } else {
            foreach ($movies as $movie) {
                $tmdb_id = $movie['id'];
                $title = $movie['title'];
                $description = $movie['overview'];
                $posterPath = !empty($movie['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $movie['poster_path'] : "https://via.placeholder.com/500x750?text=No+Image";
                $releaseDate = !empty($movie['release_date']) ? $movie['release_date'] : null;
                $price = rand(599, 1999) / 100;
                $category = !empty($movie['genre_ids']) ? getGenreName($movie['genre_ids'][0], $apiKey) : 'unknown';

                $stmt = $conn->prepare("INSERT INTO movies (tmdb_id, title, description, poster_path, release_date, price, category, created_at, updated_at) 
                                        VALUES (:tmdb_id, :title, :description, :poster_path, :release_date, :price, :category, NOW(), NOW())
                                        ON DUPLICATE KEY UPDATE 
                                            tmdb_id = VALUES(tmdb_id),
                                            updated_at = NOW(), 
                                            description = VALUES(description), 
                                            poster_path = VALUES(poster_path), 
                                            release_date = VALUES(release_date), 
                                            category = VALUES(category)");
                $stmt->execute([
                    ':tmdb_id' => $tmdb_id,
                    ':title' => $title,
                    ':description' => $description,
                    ':poster_path' => $posterPath,
                    ':release_date' => $releaseDate,
                    ':price' => $price,
                    ':category' => $category,
                ]);
            }
        }

        $searchQuery = "SELECT id, tmdb_id, title, description, poster_path, price, category FROM movies WHERE title LIKE :query OR description LIKE :query ORDER BY created_at DESC";
        $stmt = $conn->prepare($searchQuery);
        $searchTerm = '%' . $query . '%';
        $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log($e->getMessage());
        $errorMessages[] = "Une erreur est survenue lors de la récupération des films.";
    }
}

function getGenreName($genreId, $apiKey) {
    static $genres = null;

    if ($genres === null) {
        $url = "https://api.themoviedb.org/3/genre/movie/list?api_key=$apiKey&language=en-US";
        $response = file_get_contents($url);
        $genres = json_decode($response, true)['genres'] ?? [];
    }

    foreach ($genres as $genre) {
        if ($genre['id'] == $genreId) {
            return $genre['name'];
        }
    }

    return 'unknown';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche - CINEMAX</title>
    <link href="assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">CINEMAX</a>
        <div class="search-container">
            <form action="recherche.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Rechercher..." value="<?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="search-button">Rechercher</button>
            </form>
        </div>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="utilisateur/user.php">Profil</a>
                <a href="utilisateur/panier.php" class="image-swap-container"><div class="image-swap-container">
                        <img class="default" src="assets/photo/shop2.png" alt="Boutique">
                        <img class="hover" src="assets/photo/shop.png" alt="Boutique survolée">
                </div></a>
                <a href="login/logout.php">Se déconnecter</a>
            <?php else: ?>
                <a href="login/login.php">Se connecter</a>
                <a href="login/register.php">Nouveau compte</a>
            <?php endif; ?>
        </div>
    </header>
    <main>
        <h1>Résultats de recherche</h1>
        <?php if (!empty($errorMessages)): ?>
            <?php foreach ($errorMessages as $error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($searchResults)): ?>
            <div class="movie-grid">
                <?php foreach ($searchResults as $movie): ?>
                    <div class="movie-card">
                        <a href="movies.php?id=<?php echo $movie['id']; ?>">
                            <img src="<?php echo htmlspecialchars($movie['poster_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8'); ?>" class="movie-poster">
                        </a>
                        <div class="movie-info">
                            <a href="movies.php?id=<?php echo $movie['id']; ?>" class="movie-title"><?php echo htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8'); ?></a>
                            <div class="movie-price"><?php echo htmlspecialchars(number_format($movie['price'], 2), ENT_QUOTES, 'UTF-8'); ?> </div>
                            <p class="movie-category"><strong>Catégorie:</strong> <?php echo htmlspecialchars($movie['category'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="movie-desc"><?php echo htmlspecialchars(substr($movie['description'], 0, 50), ENT_QUOTES, 'UTF-8') . '...'; ?></p>
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
            <p class="no-content">Aucun résultat trouvé pour "<?php echo htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8'); ?>".</p>
        <?php endif; ?>
    </main>
</body>
</html>