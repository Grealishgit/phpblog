<?php
session_start();
require 'config/database.php';
require 'includes/functions.php';

$slug = $_GET['slug'] ?? '';
$category = $slug ? get_category_by_slug($pdo, $slug) : null;

if (!$category) {
    $page_title = 'Not Found';
    require 'includes/header.php';
    echo '<div class="empty-state">Category not found.</div>';
    require 'includes/footer.php';
    exit;
}

$posts_per_page = 5;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $posts_per_page;

$posts = get_posts($pdo, 'published', $posts_per_page, $offset, $category['slug']);
$total_posts = count_posts($pdo, 'published', $category['slug']);
$total_pages = (int) ceil($total_posts / $posts_per_page);

$page_title = $category['name'];
require 'includes/header.php';
?>

<h1 class="page-title">Category: <?= clean($category['name']) ?></h1>

<?php if (empty($posts)): ?>
    <div class="empty-state">No posts in this category yet.</div>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <article class="post-card">
            <h2><a href="/post.php?slug=<?= clean($post['slug']) ?>"><?= clean($post['title']) ?></a></h2>
            <div class="post-meta">
                By <?= clean($post['username']) ?> on <?= format_date($post['created_at']) ?>
            </div>
            <p class="post-excerpt"><?= clean($post['excerpt'] ?: mb_substr(strip_tags($post['content']), 0, 150) . '...') ?>
            </p>
        </article>
    <?php endforeach; ?>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?slug=<?= clean($category['slug']) ?>&page=<?= $i ?>"
                    class="<?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>