<?php
require 'api/db.php';
$stmt = $pdo->query("SELECT id, name FROM categories");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $pdo->query("SELECT id, title, theme FROM links");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
