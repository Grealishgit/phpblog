<?php
// $page_title and $pdo must already be set by the page that includes this file
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? clean($page_title) . ' — ' : '' ?>My PHP Blog</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="/index.php" class="logo">My PHP Blog</a>
            <nav class="main-nav">
                <a href="/index.php">Home</a>
                <?php foreach (get_categories($pdo) as $cat): ?>
                    <a href="/category.php?slug=<?= clean($cat['slug']) ?>"><?= clean($cat['name']) ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </header>
    <main class="container">