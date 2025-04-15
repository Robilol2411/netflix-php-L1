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

try {
    $url = "https://api.themoviedb.org/3/discover/movie?api_key=$apiKey&with_crew=$directorId&language=fr-FR&sort_by=release_date.desc";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (!empty($data['results'])) {
        foreach ($data['results'] as $movie) {
            $title = $movie['title'];
            $description = $movie['overview'];
            $posterPath = !empty($movie['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $movie['poster_path'] : "https://via.placeholder.com/500x750?text=No+Image";
            $releaseDate = !empty($movie['release_date']) ? $movie['release_date'] : null;
            $price = rand(599, 1999) / 100;
            $category = 'unknown'; 

            try {
                $stmt = $conn->prepare("INSERT INTO movies (title, description, poster_path, release_date, price, category, created_at, updated_at) 
                                        VALUES (:title, :description, :poster_path, :release_date, :price, :category, NOW(), NOW())
                                        ON DUPLICATE KEY UPDATE 
                                            updated_at = NOW(), 
                                            description = VALUES(description), 
                                            poster_path = VALUES(poster_path), 
                                            release_date = VALUES(release_date), 
                                            category = VALUES(category)");
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':poster_path' => $posterPath,
                    ':release_date' => $releaseDate,
                    ':price' => $price,
                    ':category' => $category,
                ]);
            } catch (Exception $e) {
                error_log("Erreur lors de l'insertion du film : " . $e->getMessage());
            }

            $movies[] = [
                'id' => $movie['id'],
                'title' => $title,
                'poster_path' => $posterPath,
                'overview' => $description,
            ];
        }
    } else {
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