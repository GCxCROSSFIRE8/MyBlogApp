<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$post = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    $userId = $_SESSION['user_id'];
    $userRole = $_SESSION['role'] ?? 'user';

    // Admins can edit any post, regular users only their own
    if ($userRole === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    $post = $stmt->fetch();

    if (!$post) {
        echo "<div class='alert alert-danger text-center mt-5'>❌ Post not found or access denied.</div>";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            $error = "❌ Title and content cannot be empty.";
        } else {
            $updateStmt = $conn->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");
            $updateStmt->execute([
                'title' => $title,
                'content' => $content,
                'id' => $id
            ]);
            header("Location: dashboard.php");
            exit();
        }
    }
} else {
    echo "<div class='alert alert-danger mt-5 text-center'>❌ Invalid post ID!</div>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container mt-5">
    <h3 class="mb-4">✏️ Edit Post</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>
        <div class="mb-3">
            <textarea class="form-control" name="content" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Update</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

