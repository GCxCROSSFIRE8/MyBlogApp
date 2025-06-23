<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log-in.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Use prepared statement to delete only if user owns the post
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $id,
            ':user_id' => $user_id
        ]);

        if ($stmt->rowCount() > 0) {
            header("Location: dashboard.php?deleted=1");
            exit();
        } else {
            echo "<div class='alert alert-danger text-center'>⚠️ You cannot delete this post or it does not exist.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger text-center'>⚠️ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger text-center'>⚠️ Invalid post ID.</div>";
}
?>
