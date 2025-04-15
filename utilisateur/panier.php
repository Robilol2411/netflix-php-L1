<?php
session_start();
require '../baseDD/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$messagesErreur = [];
$messageSucces = '';
$panier = [];
$total = 0;

function updateCartSession($conn, $userId) {
    $query = "SELECT c.id as cart_id, c.quantity, m.id, m.title, m.poster_path, m.price 
              FROM cart c 
              JOIN movies m ON c.movie_id = m.id 
              WHERE c.user_id = :user_id 
              ORDER BY c.added_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item']) && isset($_POST['cart_id'])) {
        try {
            $cartId = (int)$_POST['cart_id'];
            $query = "DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $messageSucces = "Film retiré du panier avec succès !";
        } catch (Exception $e) {
            $messagesErreur[] = "Erreur lors de la suppression.";
        }
    }

    if (isset($_POST['empty_cart'])) {
        try {
            $query = "DELETE FROM cart WHERE user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $messageSucces = "Votre panier a été vidé avec succès !";
        } catch (Exception $e) {
            $messagesErreur[] = "Erreur lors du vidage du panier.";
        }
    }

    if (isset($_POST['confirm_purchase'])) {
        try {
            $conn->beginTransaction();
            $query = "SELECT movie_id, quantity FROM cart WHERE user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($cartItems) > 0) {
                foreach ($cartItems as $item) {
                    $priceQuery = "SELECT price FROM movies WHERE id = :movie_id";
                    $stmt = $conn->prepare($priceQuery);
                    $stmt->bindParam(':movie_id', $item['movie_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $movieData = $stmt->fetch(PDO::FETCH_ASSOC);

                    $insertQuery = "INSERT INTO purchases (user_id, movie_id, price, purchase_date, status) 
                                    VALUES (:user_id, :movie_id, :price, NOW(), 'completed')";
                    $stmt = $conn->prepare($insertQuery);
                    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':movie_id', $item['movie_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':price', $movieData['price'], PDO::PARAM_STR);
                    $stmt->execute();
                }

                $query = "DELETE FROM cart WHERE user_id = :user_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();

                $conn->commit();
                $messageSucces = "Votre achat a été effectué avec succès !";
            } else {
                $messagesErreur[] = "Votre panier est vide !";
                $conn->rollBack();
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $messagesErreur[] = "Erreur lors de l'achat.";
        }
    }
}

try {
    $panier = updateCartSession($conn, $_SESSION['user_id']);
    foreach ($panier as $item) {
        $total += $item['price'] * $item['quantity'];
    }
} catch (Exception $e) {
    $messagesErreur[] = "Erreur lors de la récupération du panier.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - CINEMAX</title>
    <link href="../assets/style_index.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <a href="../index.php" class="logo">CINEMAX</a>
        <div class="search-container">
            <form action="../recherche.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Rechercher...">
                <button type="submit">Rechercher</button>
            </form>
        </div>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../utilisateur/user.php">Profil</a>
                <a href="../login/logout.php">Se déconnecter</a>
            <?php else: ?>
                <a href="../login/login.php">Se connecter</a>
                <a href="../login/register.php">Nouveau compte</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <?php if (!empty($messagesErreur)): ?>
            <?php foreach ($messagesErreur as $erreur): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($messageSucces)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($messageSucces, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="cart-container">
            <h1>Mon Panier</h1>
            <?php if (empty($panier)): ?>
                <div class="cart-empty">
                    <p>Votre panier est vide</p>
                    <a href="../index.php" class="cart-empty-link">Découvrir nos films</a>
                </div>
            <?php else: ?>
                <div class="cart-header">
                    <h3><?php echo count($panier); ?> film(s) dans votre panier</h3>
                    <form method="POST">
                        <button type="submit" name="empty_cart" class="cart-empty-btn">Vider le panier</button>
                    </form>
                </div>
                <div class="cart-items">
                    <?php foreach ($panier as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['poster_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>" class="cart-item-img">
                            <div class="cart-item-info">
                                <div>
                                    <div class="cart-item-title"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="cart-item-price"><?php echo htmlspecialchars(number_format($item['price'], 2), ENT_QUOTES, 'UTF-8'); ?> €</div>
                                </div>
                                <div>
                                    <small>Quantité: <?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </div>
                            <div class="cart-item-actions">
                                <form method="POST">
                                    <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($item['cart_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" name="remove_item" class="cart-remove-btn">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-summary">
                    <div class="cart-total">Total: <?php echo htmlspecialchars(number_format($total, 2), ENT_QUOTES, 'UTF-8'); ?> €</div>
                    <form method="POST">
                        <button type="submit" name="confirm_purchase" class="cart-checkout-btn">Confirmer l'achat</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>