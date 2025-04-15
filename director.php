<?php
session_start();
require 'baseDD/database.php';
require 'baseDD/envapi.php';

if (!isset($_GET['director_id']) || !is_numeric($_GET['director_id'])) {
    die('Réalisateur non trouvé.');
}

$directorId = (int)$_GET['director_id'];
$directorName = htmlspecialchars($_GET['director_name'], ENT_QUOTES, 'UTF-8');
$apiKey = $TMDB_API_KEY;
$errorMessages = [];
$movies = [];

// Verify the director details
try {
    $directorDetailsUrl = "https://api.themoviedb.org/3/person/$directorId?api_key=$apiKey&language=fr-FR";
    $directorDetailsResponse = file_get_contents($directorDetailsUrl);
    $directorDetails = json_decode($directorDetailsResponse, true);

    if (empty($directorDetails) || $directorDetails['name'] !== $directorName) {
        die('Le réalisateur spécifié est introuvable ou ne correspond pas.');
    }
} catch (Exception $e) {
    die('Erreur lors de la vérification du réalisateur.');
}

// Handle adding movies to the cart
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
        header('Location: director.php?director_id=' . urlencode($directorId) . '&director_name=' . urlencode($directorName) . '&added=1');
        exit;
    } catch (Exception $e) {
        error_log($e->getMessage());
        $errorMessages[] = "Une erreur est survenue. Veuillez réessayer.";
    }
}
try {
    $url = "https://api.themoviedb.org/3/person/$directorId/movie_credits?api_key=$apiKey&language=fr-FR";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (!empty($data['crew'])) {
        // Filter only movies where the person is the director
        $directedMovies = array_filter($data['crew'], function($movie) {
            return $movie['job'] === 'Director';
        });

        foreach ($directedMovies as $movie) {
            $tmdb_id = $movie['id'];
            $title = $movie['title'];
            $description = $movie['overview'];
            $posterPath = !empty($movie['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $movie['poster_path'] : "https://via.placeholder.com/500x750?text=No+Image";
            $releaseDate = !empty($movie['release_date']) ? $movie['release_date'] : null;
            $price = rand(599, 1999) / 100;
            $category = 'unknown';

            try {
                // Check if movie already exists by TMDB ID
                $checkStmt = $conn->prepare("SELECT id FROM movies WHERE tmdb_id = :tmdb_id");
                $checkStmt->execute([':tmdb_id' => $tmdb_id]);
                $existingMovie = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($existingMovie) {
                    // Movie exists, use existing ID
                    $movies[] = [
                        'id' => $existingMovie['id'],
                        'tmdb_id' => $tmdb_id,
                        'title' => $title,
                        'poster_path' => $posterPath,
                        'overview' => $description
                    ];
                } else {
                    // Insert new movie
                    $stmt = $conn->prepare("INSERT INTO movies (tmdb_id, title, description, poster_path, release_date, price, category, created_at, updated_at) 
                                        VALUES (:tmdb_id, :title, :description, :poster_path, :release_date, :price, :category, NOW(), NOW())");
                    $stmt->execute([
                        ':tmdb_id' => $tmdb_id,
                        ':title' => $title,
                        ':description' => $description,
                        ':poster_path' => $posterPath,
                        ':release_date' => $releaseDate,
                        ':price' => $price,
                        ':category' => $category
                    ]);

                    $newId = $conn->lastInsertId();
                    $movies[] = [
                        'id' => $newId,
                        'tmdb_id' => $tmdb_id,
                        'title' => $title,
                        'poster_path' => $posterPath,
                        'overview' => $description
                    ];
                }
            } catch (Exception $e) {
                error_log("Erreur lors de l'insertion du film : " . $e->getMessage());
            }
        }
    }

    if (empty($movies)) {
        $errorMessages[] = "Aucun film trouvé pour ce réalisateur.";
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $errorMessages[] = "Une erreur est survenue lors de la récupération des films.";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Films de <?php echo $directorName; ?> - CINEMAX</title>
    <link href="assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">CINEMAX</a>
        <div class="search-container">
            <form action="recherche.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Rechercher...">
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
        <h1>Films de <?php echo $directorName; ?></h1>
        <?php if (!empty($errorMessages)): ?>
            <?php foreach ($errorMessages as $error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($movies)): ?>
            <div class="movie-grid">
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <a href="movies.php?id=<?php echo htmlspecialchars($movie['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="<?php echo htmlspecialchars($movie['poster_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8'); ?>" class="movie-poster">
                        </a>
                        <div class="movie-info">
                            <a href="movies.php?id=<?php echo htmlspecialchars($movie['id'], ENT_QUOTES, 'UTF-8'); ?>" class="movie-title"><?php echo htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8'); ?></a>
                            <p class="movie-desc"><?php echo htmlspecialchars(substr($movie['overview'], 0, 100), ENT_QUOTES, 'UTF-8'); ?>...</p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST" class="add-to-cart-form">
                                    <input type="hidden" name="movie_id" value="<?php echo htmlspecialchars($movie['id'], ENT_QUOTES, 'UTF-8'); ?>">
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
            <p class="no-content">Aucun film trouvé pour ce réalisateur.</p>
        <?php endif; ?>
    </main>
</body>
</html>