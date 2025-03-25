<?php 
require 'env.php';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname",$user,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOxception $e){ ?>
  <p> Votre base de donnée n'est pas connectée </p>
<?php } ?>