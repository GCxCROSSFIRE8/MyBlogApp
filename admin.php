<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: log-in.php');
    exit();
}

if (isset($_SESSION['flash_success'])) {
    echo "<div class='alert alert-success text-center'>{$_SESSION['flash_success']}</div>";
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    echo "<div class='alert alert-danger text-center'>{$_SESSION['flash_error']}</div>";
    unset($_SESSION['flash_error']);
}

try {
    $stmt = $conn->prepare("
        SELECT posts.*, users.name, users.email AS author_email
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.deleted_at IS NULL
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Manage All Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2>Admin Panel: All Posts Management</h2>

    <?php if (!$posts): ?>
        <div class="alert alert-info">No posts available.</div>
    <?php else: ?>
        <?php foreach ($posts as $p): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?=htmlspecialchars($p['title'])?></h5>
                    <p class="mb-1"><strong>By:</strong> <?=htmlspecialchars($p['author_email'])?></p>
                    <p class="text-muted mb-1">Created: <?=htmlspecialchars($p['created_at'])?> | Updated: <?=htmlspecialchars($p['updated_at'])?></p>
                    <p><?=nl2br(htmlspecialchars($p['content']))?></p>

                    <a href="edit.php?id=<?=$p['id']?>&admin=1" class="btn btn-sm btn-outline-primary">Edit</a>
                    <a href="delete.php?id=<?=$p['id']?>&admin=1" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post?')">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

