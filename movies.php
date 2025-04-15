<?php
session_start();
require 'baseDD/database.php';
require 'baseDD/envapi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Film non trouvé.');
}

$movieId = (int)$_GET['id'];
$apiKey = $TMDB_API_KEY;

try {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = :id");
    $stmt->bindParam(':id', $movieId, PDO::PARAM_INT);
    $stmt->execute();
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        die('Film introuvable.');
    }

    $url = "https://api.themoviedb.org/3/movie/{$movie['tmdb_id']}/credits?api_key=$apiKey";
    $response = @file_get_contents($url);

    if ($response === false) {
        throw new Exception("Impossible de récupérer les crédits pour ce film.");
    }

    $credits = json_decode($response, true);

    $director = null;
    if (!empty($credits['crew'])) {
        foreach ($credits['crew'] as $crewMember) {
            if ($crewMember['job'] === 'Director') {
                $director = $crewMember;
                break;
            }
        }
    }

    $mainActors = [];
    if (!empty($credits['cast'])) {
        $mainActors = array_slice($credits['cast'], 0, 5);
    }

} catch (Exception $e) {
    error_log("Erreur API TMDB : " . $e->getMessage());
    $director = null;
    $mainActors = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - CINEMAX</title>
    <link href="assets/style_movies.css" rel="stylesheet">
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
                    <div class="image-swap-container">
                        <img class="default" src="assets/photo/shop2.png" alt="Boutique">
                        <img class="hover" src="assets/photo/shop.png" alt="Boutique survolée">
                    </div>
                </a>
                <a href="login/logout.php">Se déconnecter</a>
            <?php else: ?>
                <a href="login/login.php">Se connecter</a>
                <a href="login/register.php">Nouveau compte</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="movie-container">
        <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
        <div class="movie-content">
            <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" class="movie-poster">
            <div class="movie-info">
                <p class="description"><?php echo htmlspecialchars($movie['description']); ?></p>
                <p class="price">Prix : <strong><?php echo htmlspecialchars(number_format($movie['price'], 2)); ?> €</strong></p>

                <?php if ($director): ?>
                    <p class="director">Réalisateur : <strong><?php echo htmlspecialchars($director['name']); ?></strong></p>
                    <a href="director.php?director_id=<?php echo htmlspecialchars($director['id']); ?>&director_name=<?php echo urlencode($director['name']); ?>" class="director-btn">
                        Voir les films de <?php echo htmlspecialchars($director['name']); ?>
                    </a>
                <?php else: ?>
                    <p class="director">Réalisateur : Non disponible.</p>
                <?php endif; ?>

                <?php if (!empty($mainActors)): ?>
                    <div class="actors">
                        <h3>Acteurs principaux :</h3>
                        <ul>
                            <?php foreach ($mainActors as $actor): ?>
                                <li><?php echo htmlspecialchars($actor['name']); ?> (<?php echo htmlspecialchars($actor['character']); ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="index.php">
                    <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                    <button type="submit" name="add_to_cart" class="director-btn">Ajouter au panier</button>
                </form>
            <?php else: ?>
                <a href="login/login.php" class="director-btn">Connectez-vous pour acheter</a>
            <?php endif; ?>
    </main>
</body>
</html>
