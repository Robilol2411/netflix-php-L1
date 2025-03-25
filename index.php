<?php
session_start();
require 'baseDD/database.php';

$articles = [];
$users = [];
$errorMessages = [];

// Articles Retrieval
try {
    $articleQuery = '
        SELECT article.id, article.userId, article.title, article.image, 
               article.created_by, article.description, article.created_at AS article_date,
               user.firstName, user.lastName
        FROM article
        LEFT JOIN user ON article.userId = user.id
        ORDER BY article.created_at DESC
    ';
    $stmt = $conn->prepare($articleQuery);
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessages[] = "Error retrieving articles: " . $e->getMessage();
}

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
    <title>CRUD Application</title>
    <link href="assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="#" class="logo">CRUD</a>
        <div class="nav-links">
            <a href="articles/articles.php">Articles</a>
            <?php 
            if (isset($_SESSION['user_id'])) {
                echo '<a href="articles/create_article.php">Create Article</a>';
            } 
            ?>
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

    <main>
        <section>
        <h1>Les Articles :</h1>

        <?php if ($articles > 10): ?>
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
        </section>

        <section>
            <h1>Users</h1>
            <?php if (!empty($errorMessages)): ?>
                <div class="error-messages">
                    <?php foreach ($errorMessages as $error): ?>
                        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                            <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <div class="btn-container">
                                    <a class="btn btn-edit" href="editUser.php?id=<?php echo htmlspecialchars($user['id']); ?>">Edit</a>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user['id']): ?>
                                        <a class="btn btn-delete" 
                                           href="deleteUser.php?id=<?php echo htmlspecialchars($user['id']); ?>" 
                                           onclick="return confirm('Are you sure you want to delete this user?');">
                                            Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>