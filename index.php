<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Projet PHP Simple</title>
    <link href="assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="#" class="logo">CRUD</a>
        <div class="nav-links">
            <a href="articles/articles.php">Articles</a>
            <a href="articles/create.php">Create article</a>
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
</body>
</html>