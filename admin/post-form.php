<?php
session_start();
require '../config/database.php';
require '../includes/functions.php';

require_login();

$post = null;
$is_edit = false;

if (isset($_GET['id'])) {
    $post = get_post_by_id($pdo, (int)$_GET['id']);
    if ($post) {
        $is_edit = true;
    }
}

$categories = get_categories($pdo);
$page_title = $is_edit ? 'Edit Post' : 'New Post';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= clean($page_title) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="dashboard.php" class="logo">Admin Panel</a>
            <nav class="main-nav">
                <a href="dashboard.php">&larr; Back to Dashboard</a>
            </nav>
        </div>
    </header>
    <main class="container">
        <h1 class="page-title"><?= clean($page_title) ?></h1>

        <form method="POST" action="post-save.php">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required value="<?= clean($post['title'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id">
                    <option value="">— None —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= (isset($post['category_id']) && $post['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= clean($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Excerpt (short summary, optional)</label>
                <input type="text" name="excerpt" value="<?= clean($post['excerpt'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Content</label>
                <textarea name="content" required><?= clean($post['content'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="draft"
                        <?= (isset($post['status']) && $post['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
                    <option value="published"
                        <?= (isset($post['status']) && $post['status'] === 'published') ? 'selected' : '' ?>>Published
                    </option>
                </select>
            </div>

            <button type="submit">Save Post</button>
        </form>
    </main>
</body>

</html>