
<?php require "../baseDD/database.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Projet PHP Simple</title>
    <link href="../assets/style.css" rel="stylesheet"></link>
</head>
<body>
    <div class="center">
        <form class="form" action="create.php" method="post">
        <span class="input-span">
            <label for="email" class="label">Email</label>
            <input type="email" name="email" id="email"
        /></span>
        <span class="input-span">
            <label for="username" class="label">Username</label>
            <input type="text" name="username" id="username"
        /></span>
        <span class="input-span">
            <label for="password" class="label">Password</label>
            <input type="password" name="password" id="password"
        /></span>
        </span>
        <input class="submit" type="submit" value="Create user" />
        <span class="span">You have an account? <a href="../index.php">Log in</a></span>
        </form>
    </div>
    <?php 
        $erreur = 0;
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["email"]) && !empty($_POST["username"]) && !empty($_POST["password"])) {
            $email = trim($_POST["email"]);
            if (!isset($conn)) {
                die("Erreur de connexion à la base de données.");
            }
            $sql = "SELECT COUNT(*) FROM user WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                ?> <p>Email déjà existant</p> <?php
                $errmail = 1;
            } else {
                $errmail = 0;
            }
            $username = trim($_POST["username"]);
            if (!isset($conn)) {
                die("Erreur de connexion à la base de données.");
            }
            $sql = "SELECT COUNT(*) FROM user WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username]);
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                ?> <p>username déjà existant</p> <?php
                $erruser = 1;
            } else {
                $erruser = 0;
            }
            $password = trim($_POST["password"]);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $description = trim($_POST["description"]);
            if ($erruser == 0 && $errmail == 0){
                $stmt = $conn->prepare("INSERT INTO user (email,username,password,description) VALUES (?,?,?,?)");
                $result = $stmt->execute([$email,$username,$hashed_password,$description]);
            }
        } else {
            ?> <p> Un champ n'est pas remplie </p> <?php
        }
        $sql = "SELECT id, email, username, description FROM user";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>   
    </div>
</body>
</html>