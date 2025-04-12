<?php 
ob_start();
session_start();
require "../baseDD/database.php"; 
$registration_error = '';
$registration_success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && 
    !empty($_POST["email"]) && 
    !empty($_POST["firstname"]) && 
    !empty($_POST["lastname"]) && 
    !empty($_POST["password"])) {
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $password = trim($_POST["password"]);
    if (!$email) {
        $registration_error = "Format d'email invalide";
    } else {
        if (!isset($conn)) {
            die("Erreur de connexion à la base de données.");
        }
        $sql = "SELECT COUNT(*) FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $registration_error = "Email déjà existant";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (firstname, lastname, email, password, userRole) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$firstname, $lastname, $email, $hashed_password, 'user']);
            if ($result) {
                $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                $_SESSION['email'] = $email;
                $_SESSION['login_success'] = true;
                ob_end_clean();
                header("Location: ../index.php");
                exit();
            } else {
                $registration_error = "Erreur lors de l'inscription";
            }
        }
    }
} else {
    $registration_error = "Tous les champs sont requis";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="../assets/style_index.css" rel="stylesheet">
</head>
<body>
<header>
    <a href="..\index.php">Acceuil</a>
</header>
    <div class="center">
        <form class="form" action="register.php" method="post">
            <span class="input-span">
                <label for="email" class="label">Email</label>
                <input type="email" name="email" id="email" required 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                >
            </span>
            <span class="input-span">
                <label for="firstname" class="label">Prénom</label>
                <input type="text" name="firstname" id="firstname" required
                       value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>"
                >
            </span>
            <span class="input-span">
                <label for="lastname" class="label">Nom</label>
                <input type="text" name="lastname" id="lastname" required
                       value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>"
                >
            </span>
            <span class="input-span">
                <label for="password" class="label">Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </span>
            <input class="submit" type="submit" value="Créer un compte" />
            <span class="span">Vous avez un compte? <a href="login.php">Connectez-vous</a></span>
            
            <?php if (!empty($registration_error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($registration_error); ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
<?php 
ob_end_flush(); 
?>