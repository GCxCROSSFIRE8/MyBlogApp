<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log-in.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? 'user';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$post_id = (int)$_GET['id'];

try {
    // Fetch post to check existence
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id AND deleted_at IS NULL");
    $stmt->execute([':id' => $post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        $_SESSION['flash_error'] = "Post not found.";
        header("Location: dashboard.php");
        exit();
    }

    // Only admin can delete
    if ($user_role !== 'admin') {
        $_SESSION['flash_error'] = "❌ You can't delete this post.";
        header("Location: dashboard.php");
        exit();
    }

    // Soft delete post by setting deleted_at timestamp
    $deleteStmt = $conn->prepare("UPDATE posts SET deleted_at = NOW() WHERE id = :id");
    $deleteStmt->execute([':id' => $post_id]);

    $_SESSION['flash_success'] = "✅ Post deleted successfully!";
    header("Location: dashboard.php");
    exit();

} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
