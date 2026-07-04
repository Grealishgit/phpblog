<?php
session_start();
require '../config/database.php';
require '../includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');
$excerpt = trim($_POST['excerpt'] ?? '');
$content = trim($_POST['content'] ?? '');
$category_id = $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
$status = in_array($_POST['status'], ['draft', 'published']) ? $_POST['status'] : 'draft';

if ($title === '' || $content === '') {
    die('Title and content are required.');
}

$slug = slugify($title);

// Make sure the slug is unique (append -2, -3, etc. if needed)
$base_slug = $slug;
$counter = 2;
while (true) {
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = :slug AND id != :id");
    $stmt->execute([':slug' => $slug, ':id' => $id ?? 0]);
    if (!$stmt->fetch()) break;
    $slug = $base_slug . '-' . $counter;
    $counter++;
}

if ($id) {
    // UPDATE existing post
    $stmt = $pdo->prepare("UPDATE posts
    SET title = :title, slug = :slug, excerpt = :excerpt, content = :content,
    category_id = :category_id, status = :status
    WHERE id = :id");
    $stmt->execute([
        ':title'       => $title,
        ':slug'        => $slug,
        ':excerpt'     => $excerpt,
        ':content'     => $content,
        ':category_id' => $category_id,
        ':status'      => $status,
        ':id'          => $id,
    ]);
} else {
    // INSERT new post
    $stmt = $pdo->prepare("INSERT INTO posts (title, slug, excerpt, content, category_id, user_id, status)
    VALUES (:title, :slug, :excerpt, :content, :category_id, :user_id, :status)");
    $stmt->execute([
        ':title'       => $title,
        ':slug'        => $slug,
        ':excerpt'     => $excerpt,
        ':content'     => $content,
        ':category_id' => $category_id,
        ':user_id'     => $_SESSION['user_id'],
        ':status'      => $status,
    ]);
}

header('Location: dashboard.php?saved=1');
exit;