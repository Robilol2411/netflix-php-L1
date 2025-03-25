<?php
session_start();
require '../baseDD/database.php';
$articles = [];
$errorMessage = '';

try {
    $query = '
        SELECT article.userId, article.title, article.image, article.created_by, article.description, article.created_at AS article_date,
               user.firstName, user.lastName
        FROM article
        LEFT JOIN user ON article.userId = user.id
    ';
    $articles = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errorMessage = "Erreur lors de la récupération des articles ou des utilisateurs : " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Projet PHP Simple</title>
    <link href="../assets/style_index.css" rel="stylesheet">
</head>
<body>
<header class="navbar">
        <a href="../index.php" class="logo">CRUD</a>
        <div class="nav-links">
            <?php 
            if (isset($_SESSION['user_id'])) {
                echo '<a href="create_article.php">Create article</a>';
            } ?>
            <?php
            if (!isset($_SESSION['user_id'])) {
                echo '<a href="login/login.php">Login</a>';
                echo '<a href="login/register.php">Register</a>';
            } else {
                echo '<a href="login/logout.php">Logout</a>';
            }
            ?>
        </div>
    </header>

    <h1>Les Articles :</h1>

    <?php if (!empty($articles)): ?>
        <?php foreach ($articles as $article): ?>
            <div class="articles">
                <p>
                    Titre : <?php echo htmlspecialchars($article['title']); ?><br><br>
                    Description : <?php echo htmlspecialchars($article['description']); ?><br><br>
                    Auteur : <?php echo htmlspecialchars($article['created_by']); ?><br>
                    Date de publication : <?php echo date('d/m/Y H:i', strtotime($article['article_date'])); ?><br>
                </p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun article disponible.</p>
    <?php endif; ?>

    <?php if (isset($errorMessage)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>


</body>
</html>

