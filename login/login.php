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
    <link href="../assets/style_login.css" rel="stylesheet">

</head>
<body>
<header class="navbar">
        <a href="..\index.php" class="logo">CINEMAX</a>
        <div class="search-container">
        </div>
    </header>
    <div class="center">
        <form class="form" action="login.php" method="post">
        <span class="input-span">
            <label for="email" class="label">Email</label>
            <input type="email" name="email" id="email">
        </span>
        <span class="input-span">
            <label for="password" class="label">Password</label>
            <input type="password" name="password" id="password">
        </span>
        <input class="submit" type="submit" value="Log in">
        <span class="span">Vous n'avez pas de <a href="register.php">compte </a>?</span>
        </form>
    </div>
    <?php 
    $login_error = ''; 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["email"]) && !empty($_POST["password"])) {
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        if (!isset($conn)) {
            die("Erreur de connexion à la base de données.");
        }
        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            ob_end_clean();
            header("Location: ../index.php");
            exit(); 
        } else {
            $login_error = "Mot de passe ou email incorrect";
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