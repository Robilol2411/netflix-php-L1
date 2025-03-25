<?php
session_start();
require 'baseDD/database.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$userIdToDelete = (int)$_GET['id'];
if ($_SESSION['user_id'] == $userIdToDelete) {
    die("Vous ne pouvez pas supprimer votre propre compte.");
}

try {
    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt->execute([$userIdToDelete]);
    header("Location: index.php");
    exit();
} catch (PDOException $e) {
    echo "Erreur lors de la suppression : " . $e->getMessage();
}
?>