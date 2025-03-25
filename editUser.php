<?php
session_start();
require 'baseDD/database.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$userId = (int)$_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];

    try {
        $stmt = $conn->prepare("UPDATE user SET firstname = ?, lastname = ?, email = ? WHERE id = ?");
        $stmt->execute([$firstname, $lastname, $email, $userId]);
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour : " . $e->getMessage();
    }
} else {
    $stmt = $conn->prepare("SELECT firstname, lastname, email FROM user WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur</title>
</head>
<body>
    <h1>Modifier Utilisateur</h1>
    <form method="POST">
        <label>Prénom :</label>
        <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
        <br>
        <label>Nom :</label>
        <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
        <br>
        <label>Email :</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <br>
        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>