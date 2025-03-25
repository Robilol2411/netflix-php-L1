<?php
session_start();
require_once '../baseDD/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'contenue_article', FILTER_SANITIZE_STRING);
    $userId = $_SESSION['user_id'];

    try {
        $userQuery = $conn->prepare("SELECT email FROM user WHERE id = :user_id");
        $userQuery->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $userQuery->execute();
        $userData = $userQuery->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            throw new Exception("Utilisateur non trouvé");
        }
        $userEmail = $userData['email'];
    } catch (Exception $e) {
        $errorMessage = "Erreur de récupération de l'utilisateur : " . $e->getMessage();
        exit();
    }

    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../uploads/'; 
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $uniqueFileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $imagePath = 'uploads/' . $uniqueFileName;
            }
        }
    }

    $sql = "INSERT INTO article (userId, title, image, description, created_by, updated_by) 
            VALUES (:userId, :title, :image, :description, :created_by, :updated_by)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $userEmail, PDO::PARAM_STR);
        $stmt->bindParam(':updated_by', $userEmail, PDO::PARAM_STR);

        $result = $stmt->execute();

        if ($result) {
            header('Location: articles.php?success=1');
            exit();
        } else {
            $errorMessage = "Erreur lors de la création de l'article.";
        }
    } catch (PDOException $e) {
        error_log('Article creation error: ' . $e->getMessage());
        $errorMessage = "Erreur lors de la création de l'article. Détails : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Article</title>
    <link href="../assets/style_article.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="../index.php" class="logo">CRUD</a>
        <div class="nav-links">
            <a href="../articles/articles.php">Articles</a>
        </div>
    </header>

    <main>
        <h1>Créer un Nouvel Article</h1>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message" style="color: red; margin-bottom: 20px;">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form class="form" action="create_article.php" method="post" enctype="multipart/form-data">
            <div class="input-container">
                <label for="title" class="label">Titre de votre article :</label>
                <input type="text" name="title" id="title" required placeholder="Entrez le titre" maxlength="255">
            </div>
            <div class="input-container">
                <label for="contenue_article" class="label">Contenu de votre article :</label>
                <textarea name="contenue_article" id="contenue_article" rows="4" required placeholder="Entrez le contenu de l'article"></textarea>
            </div>
            <div class="input-container">
                <label for="image" class="label">Image pour votre article :</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>
            <div class="form-action">
                <input class="submit" type="submit" value="Envoyer">
            </div>
        </form>
    </main>
</body>
</html>