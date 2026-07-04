<?php
session_start();
require 'config/database.php';
require 'includes/functions.php';

$slug = $_GET['slug'] ?? '';
$post = $slug ? get_post_by_slug($pdo, $slug) : null;

// Only show published posts to the public; drafts return a 404-style message
if (!$post || $post['status'] !== 'published') {
    $page_title = 'Not Found';
    require 'includes/header.php';
    echo '<div class="empty-state">Post not found.</div>';
    require 'includes/footer.php';
    exit;
}

$page_title = $post['title'];
require 'includes/header.php';
?>

<article class="single-post">
    <h1><?= clean($post['title']) ?></h1>
    <div class="post-meta">
        By <?= clean($post['username']) ?>
        on <?= format_date($post['created_at']) ?>
        <?php if ($post['category_name']): ?>
            in <?= clean($post['category_name']) ?>
        <?php endif; ?>
    </div>
    <div class="post-content">
        <?= nl2br(clean($post['content'])) ?>
    </div>
</article>

<p><a href="/index.php">&larr; Go Back to all posts</a></p>

<?php require 'includes/footer.php'; ?>