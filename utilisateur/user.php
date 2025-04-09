<?php
session_start();
require '../baseDD/database.php';

$articles = [];
$users = [];
$errorMessages = [];

// Users Retrieval
try {
    if (!isset($conn) || $conn === null) {
        throw new Exception("Database connection not established");
    }
    
    $userQuery = "SELECT id, firstname, lastname, email FROM user ORDER BY lastname, firstname";
    $stmt = $conn->prepare($userQuery);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $errorMessages[] = "Error retrieving users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location de films</title>
    <link href="../assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="#" class="logo">LoueTonFilm.com</a>
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
                echo '<a href="articles/create_article.php" class="image-swap-container"><div class="image-swap-container">
                            <img class="default" src="../assets/photo/shop2.png" alt="Default image">
                            <img class="hover" src="../assets/photo/shop.png" alt="Hover image">
                    </div></a>';
            } 
            ?>
            <?php
            echo '<a href="login/logout.php">Se déconnecter</a>';
            ?>
        </div>
    </header>

    <main>
        <h2>Vos films :</h2>
        <div>

        </div>
        <h2>Information sur votre compte:</h2>
        <div>
            <p>
                <strong>Nom:</strong> <?php echo htmlspecialchars($_SESSION['lastname']); ?><br>
                <strong>Prénom:</strong> <?php echo htmlspecialchars($_SESSION['firstname']); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?><br>
                <strong>ID:</strong> <?php echo htmlspecialchars($_SESSION['user_id']); ?><br>
                <strong>Inscrit le:</strong> <?php echo htmlspecialchars($_SESSION['created_at']); ?><br>
        </div>
    </main>
</body>
</html>