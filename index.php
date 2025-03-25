<?php
session_start();
require 'baseDD/database.php'; 
try {
    if (!isset($conn) || $conn === null) {
        throw new Exception("Database connection not established");
    }
    $stmt = $conn->prepare("SELECT firstname, lastname, email FROM user");
    $stmt->execute();
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $user = [];
    $error_message = "ERROR: Could not retrieve user. " . $e->getMessage();
}
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

    <h1>Users List</h1>
    
    <?php if (isset($error_message)): ?>
        <div class="error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($user)): ?>
                <?php foreach ($user as $users): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($users['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($users['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($users['email']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>