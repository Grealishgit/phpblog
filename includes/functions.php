<?php
function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = @iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return $text !== '' ? $text : 'n-a';
}

function clean($string)
{
    return htmlspecialchars(trim($string ?? ''), ENT_QUOTES, 'UTF-8');
}

function format_date($date)
{
    return date('F j, Y', strtotime($date));
}

function get_categories($pdo)
{
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    return $stmt->fetchAll();
}

function get_category_by_slug($pdo, $slug)
{
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch();
}

function get_posts($pdo, $status = 'published', $limit = 10, $offset = 0, $category_slug = null)
{
    $sql = "SELECT posts.*, categories.name AS category_name, categories.slug AS category_slug, users.username
            FROM posts
            LEFT JOIN categories ON posts.category_id = categories.id
            LEFT JOIN users ON posts.user_id = users.id
            WHERE 1=1";
    $params = [];

    if ($status !== 'all') {
        $sql .= " AND posts.status = :status";
        $params[':status'] = $status;
    }
    if ($category_slug) {
        $sql .= " AND categories.slug = :cat_slug";
        $params[':cat_slug'] = $category_slug;
    }

    $sql .= " ORDER BY posts.created_at DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function count_posts($pdo, $status = 'published', $category_slug = null)
{
    $sql = "SELECT COUNT(*) AS total FROM posts
            LEFT JOIN categories ON posts.category_id = categories.id
            WHERE 1=1";
    $params = [];

    if ($status !== 'all') {
        $sql .= " AND posts.status = :status";
        $params[':status'] = $status;
    }
    if ($category_slug) {
        $sql .= " AND categories.slug = :cat_slug";
        $params[':cat_slug'] = $category_slug;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetch()['total'];
}

function get_post_by_slug($pdo, $slug)
{
    $stmt = $pdo->prepare("SELECT posts.*, categories.name AS category_name, users.username
                           FROM posts
                           LEFT JOIN categories ON posts.category_id = categories.id
                           LEFT JOIN users ON posts.user_id = users.id
                           WHERE posts.slug = :slug LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    return $stmt->fetch();
}

function get_post_by_id($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
