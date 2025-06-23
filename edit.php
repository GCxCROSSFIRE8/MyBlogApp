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
$isAdminEdit = ($user_role === 'admin' && isset($_GET['admin']) && $_GET['admin'] == 1);

try {
    // Fetch the post (don't restrict to user_id yet — will check manually)
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id AND deleted_at IS NULL");
    $stmt->execute([':id' => $post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        $_SESSION['flash_error'] = "Post not found.";
        header("Location: dashboard.php");
        exit();
    }

    // Permission check
    $canEdit = $isAdminEdit || $post['user_id'] == $user_id || $user_role === 'editor';

    if (!$canEdit) {
        $_SESSION['flash_error'] = "❌ You do not have permission to edit this post.";
        header("Location: dashboard.php");
        exit();
    }

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            $error = "⚠️ Title and content cannot be empty.";
        } else {
            // Append admin note if edited as admin
            if ($isAdminEdit && strpos($content, '[Edited by Admin]') === false) {
                $content .= "\n\n[Edited by Admin]";
            }

            // Update post
            $updateStmt = $conn->prepare("UPDATE posts SET title = :title, content = :content, updated_at = NOW() WHERE id = :id");
            $updateStmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':id' => $post_id
            ]);

            $success = "✅ Post updated successfully!";
            $stmt->execute([':id' => $post_id]);
            $post = $stmt->fetch();
        }
    }
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
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

<div class="container mt-4">
    <h2>✏️ Edit Post</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($post['title']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Post</button>
        <a href="<?= $isAdminEdit ? 'admin.php' : 'dashboard.php' ?>" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


''
