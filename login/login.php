<?php 
ob_start(); 
session_start(); 
require "../baseDD/database.php"; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Projet PHP Simple</title>
    <link href="../assets/style.css" rel="stylesheet">
</head>
<body>
    <div class="center">
        <form class="form" action="login.php" method="post">
        <span class="input-span">
            <label for="username" class="label">Username</label>
            <input type="text" name="username" id="username">
        </span>
        <span class="input-span">
            <label for="password" class="label">Password</label>
            <input type="password" name="password" id="password">
        </span>
        <input class="submit" type="submit" value="Log in">
        <span class="span">Back to <a href="register.php">create account</a></span>
        </form>
    </div>
    <?php 
    $login_error = ''; 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["username"]) && !empty($_POST["password"])) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        if (!isset($conn)) {
            die("Erreur de connexion à la base de données.");
        }
        $sql = "SELECT * FROM user WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            ob_end_clean();
            header("Location: ../index.php");
            exit(); 
        } else {
            $login_error = "Mot de passe ou username incorrect";
        }
    } else {
        $login_error = "Un champ n'est pas rempli";
    }
    ?>

    <?php if (!empty($login_error)): ?>
        <p><?php echo $login_error; ?></p>
    <?php endif; ?>
</body>
</html>
<?php 
ob_end_flush(); 
?>