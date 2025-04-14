<?php
session_start();
require '../baseDD/database.php';

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
    <title>Films de Romance - CINEMAX</title>
    <link href="../assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="../index.php" class="logo">CINEMAX</a>
    </header>
    <main>
        <h1>Films de Romance</h1>
        <?php if (!empty($movies)): ?>
            <div class="movie-grid">
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <img src="<?php echo htmlspecialchars($movie['poster_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8'); ?>" class="movie-poster">
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p><?php echo htmlspecialchars(substr($movie['description'], 0, 100), ENT_QUOTES, 'UTF-8'); ?>...</p>
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