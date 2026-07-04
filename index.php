<?php
session_start();
require 'config/database.php';
require 'includes/functions.php';

$page_title = 'Home';

// Simple pagination: ?page=2, ?page=3, etc.
$posts_per_page = 5;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($current_page - 1) * $posts_per_page;

$posts = get_posts($pdo, 'published', $posts_per_page, $offset);
$total_posts = count_posts($pdo, 'published');
$total_pages = (int) ceil($total_posts / $posts_per_page);

require 'includes/header.php';
?>

<h1 class="page-title">Latest Posts</h1>

<?php if (empty($posts)): ?>
    <div class="empty-state">No posts published yet. Check back soon!</div>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <article class="post-card">
            <h2><a href="/post.php?slug=<?= clean($post['slug']) ?>"><?= clean($post['title']) ?></a></h2>
            <div class="post-meta">
                By <?= clean($post['username']) ?>
                on <?= format_date($post['created_at']) ?>
                <?php if ($post['category_name']): ?>
                    in <a href="/category.php?slug=<?= clean($post['category_slug']) ?>"><?= clean($post['category_name']) ?></a>
                <?php endif; ?>
            </div>
            <p class="post-excerpt"><?= clean($post['excerpt'] ?: mb_substr(strip_tags($post['content']), 0, 150) . '...') ?>
            </p>
        </article>
    <?php endforeach; ?>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>