<?php
session_start();
require '../baseDD/database.php';

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login/login.php');
    exit;
}

$messagesErreur = [];
$messageSucces = '';
$panier = [];
$total = 0;

// Fonction pour mettre à jour le panier en session
function updateCartSession($conn, $userId) {
    $query = "SELECT c.id as cart_id, c.quantity, m.id, m.title, m.poster_path, m.price 
              FROM cart c 
              JOIN movies m ON c.movie_id = m.id 
              WHERE c.user_id = :user_id 
              ORDER BY c.added_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Action: Supprimer un élément du panier
if (isset($_POST['remove_item']) && isset($_POST['cart_id'])) {
    try {
        $cartId = $_POST['cart_id'];
        $query = "DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':cart_id', $cartId);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        $messageSucces = "Film retiré du panier avec succès !";
    } catch (Exception $e) {
        $messagesErreur[] = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Action: Vider le panier
if (isset($_POST['empty_cart'])) {
    try {
        $query = "DELETE FROM cart WHERE user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        $messageSucces = "Votre panier a été vidé avec succès !";
    } catch (Exception $e) {
        $messagesErreur[] = "Erreur lors du vidage du panier : " . $e->getMessage();
    }
}

// Action: Confirmer l'achat (déplacer les éléments du panier vers les achats)
if (isset($_POST['confirm_purchase'])) {
    try {
        $conn->beginTransaction();
        
        // Obtenir les éléments du panier
        $query = "SELECT movie_id, quantity FROM cart WHERE user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($cartItems) > 0) {
            // Insérer chaque élément comme un achat
            foreach ($cartItems as $item) {
                // Obtenir le prix actuel du film
                $priceQuery = "SELECT price FROM movies WHERE id = :movie_id";
                $stmt = $conn->prepare($priceQuery);
                $stmt->bindParam(':movie_id', $item['movie_id']);
                $stmt->execute();
                $movieData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Insérer dans la table purchases
                $insertQuery = "INSERT INTO purchases (user_id, movie_id, price, purchase_date, status) 
                                VALUES (:user_id, :movie_id, :price, NOW(), 'completed')";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->bindParam(':movie_id', $item['movie_id']);
                $stmt->bindParam(':price', $movieData['price']);
                $stmt->execute();
            }
            
            // Vider le panier après l'achat
            $query = "DELETE FROM cart WHERE user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            
            $conn->commit();
            $messageSucces = "Votre achat a été effectué avec succès !";
        } else {
            $messagesErreur[] = "Votre panier est vide !";
            $conn->rollBack();
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $messagesErreur[] = "Erreur lors de l'achat : " . $e->getMessage();
    }
}

// Récupérer le contenu du panier
try {
    $panier = updateCartSession($conn, $_SESSION['user_id']);
    
    // Calculer le total
    foreach ($panier as $item) {
        $total += $item['price'] * $item['quantity'];
    }
} catch (Exception $e) {
    $messagesErreur[] = "Erreur lors de la récupération du panier : " . $e->getMessage();
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
            <form action="recherche.php" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Rechercher...">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <button type="submit">Rechercher</button>
            </form>
        </div>
        <div class="nav-links">
            <?php 
            if (isset($_SESSION['user_id'])) {
                echo '<a href="../utilisateur/user.php">Profil</a>';
            } 
            
            if (!isset($_SESSION['user_id'])) {
                echo '<a href="login/login.php">Se connecter</a>';
                echo '<a href="login/register.php">Nouveau compte</a>';
            } else {
                echo '<a href="login/logout.php">Se déconnecter</a>';
            }
            ?>
        </div>
    </header>

    <main>
        <!-- Affichage des messages d'erreur -->
        <?php if (!empty($messagesErreur)): ?>
            <?php foreach ($messagesErreur as $erreur): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erreur); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Affichage du message de succès -->
        <?php if (!empty($messageSucces)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($messageSucces); ?></div>
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
                            <img src="<?php echo htmlspecialchars($item['poster_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="cart-item-img">
                            <div class="cart-item-info">
                                <div>
                                    <div class="cart-item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                    <div class="cart-item-price"><?php echo htmlspecialchars(number_format($item['price'], 2)); ?> €</div>
                                </div>
                                <div>
                                    <small>Quantité: <?php echo htmlspecialchars($item['quantity']); ?></small>
                                </div>
                            </div>
                            <div class="cart-item-actions">
                                <form method="POST">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <button type="submit" name="remove_item" class="cart-remove-btn">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <div class="cart-total">Total: <?php echo number_format($total, 2); ?> €</div>
                    <form method="POST">
                        <button type="submit" name="confirm_purchase" class="cart-checkout-btn">Confirmer l'achat</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>