<?php
session_start();
require '../config/database.php';
require '../includes/functions.php';

require_login();

$posts = get_posts($pdo, 'all', 100, 0);

$page_title = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="dashboard.php" class="logo">Admin Panel</a>
            <nav class="main-nav">
                <span style="color:#cbd5e1;">Hi, <?= clean($_SESSION['username']) ?></span>
                <a href="post-form.php">+ New Post</a>
                <a href="/index.php">View Site</a>
                <a href="logout.php">Log Out</a>
            </nav>
        </div>
    </header>
    <main class="container">
        <h1 class="page-title">All Posts</h1>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Post deleted.</div>
        <?php endif; ?>
        <?php if (isset($_GET['saved'])): ?>
        <div class="alert alert-success">Post saved.</div>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
        <div class="empty-state">No posts yet.
            <a href="post-form.php">Create your first post</a>.
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= clean($post['title']) ?></td>
                    <td><?= clean($post['category_name'] ?? '—') ?></td>
                    <td>
                        <span class="status-<?= $post['status'] ?>"><?= ucfirst($post['status']) ?></span>
                    </td>
                    <td><?= format_date($post['created_at']) ?></td>
                    <td>
                        <a href="post-form.php?id=<?= $post['id'] ?>">Edit</a>
                        &nbsp;|&nbsp;
                        <a href="post-delete.php?id=<?= $post['id'] ?>" onclick="return confirm('Delete this post?');"
                            style="color:#b91c1c;">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </main>
</body>

</html>