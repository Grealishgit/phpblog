<?php
session_start();
require '../config/database.php';
require '../includes/functions.php';

require_login();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute([':id' => (int)$id]);
}

header('Location: dashboard.php?deleted=1');
exit;
