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
    $_SESSION['flash_error'] = "Invalid post ID.";
    header("Location: dashboard.php");
    exit();
}

$post_id = (int)$_GET['id'];
$returnToAdmin = isset($_GET['admin']) && $_GET['admin'] == 1;

$error = '';
$success = '';

try {
    // Check if post exists and is not deleted
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id AND deleted_at IS NULL");
    $stmt->execute([':id' => $post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        $error = "⚠️ Post not found or already deleted.";
    } elseif ($user_role !== 'admin') {
        $error = "❌ You do not have permission to delete this post.";
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            // Soft delete the post
            $deleteStmt = $conn->prepare("UPDATE posts SET deleted_at = NOW() WHERE id = :id");
            $deleteStmt->execute([':id' => $post_id]);

            $success = "✅ Post deleted successfully!";
        }
    }
} catch (PDOException $e) {
    $error = "Database error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Delete Post - MyBlogApp</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  /* Color palette */
  :root {
    --primary-color: #4a90e2;
    --danger-color: #e74c3c;
    --bg-light: #f9fafb;
    --text-dark: #2c3e50;
    --shadow: rgba(0,0,0,0.12);
  }

  body {
    background-color: var(--bg-light);
    color: var(--text-dark);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  .container {
    margin-top: 5rem;
    max-width: 480px;
  }

  .card {
    border-radius: 1rem;
    box-shadow: 0 8px 20px var(--shadow);
    transition: box-shadow 0.3s ease;
    background: #fff;
  }

  .card:hover {
    box-shadow: 0 12px 28px var(--shadow);
  }

  h2 {
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 1rem;
    text-align: center;
    letter-spacing: 1.2px;
    animation: fadeInDown 0.7s ease forwards;
  }

  .alert {
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 1rem;
    animation: fadeIn 0.7s ease forwards;
  }

  .btn-danger {
    background-color: var(--danger-color);
    border: none;
    transition: background-color 0.3s ease, transform 0.2s ease;
    font-weight: 600;
  }

  .btn-danger:hover {
    background-color: #c0392b;
    transform: scale(1.05);
    box-shadow: 0 6px 15px rgba(231, 76, 60, 0.5);
  }

  .btn-secondary {
    border-radius: 50px;
    padding: 0.5rem 1.8rem;
    transition: background-color 0.3s ease;
  }

  .btn-secondary:hover {
    background-color: #ddd;
  }

  form {
    animation: fadeInUp 0.7s ease forwards;
  }

  .confirmation-text {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    text-align: center;
    color: var(--text-dark);
  }

  /* Animations */
  @keyframes fadeInDown {
    0% {
      opacity: 0;
      transform: translateY(-20px);
    }
    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeInUp {
    0% {
      opacity: 0;
      transform: translateY(20px);
    }
    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
  <div class="card p-4">
    <h2>🗑️ Delete Post Confirmation</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
      <div class="text-center mt-3">
        <a href="<?= $returnToAdmin ? 'admin.php' : 'dashboard.php' ?>" class="btn btn-secondary">
          ← Back
        </a>
      </div>
    <?php elseif ($success): ?>
      <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
      <div class="text-center mt-3">
        <a href="<?= $returnToAdmin ? 'admin.php' : 'dashboard.php' ?>" class="btn btn-primary">
          Go to <?= $returnToAdmin ? 'Admin Panel' : 'Dashboard' ?>
        </a>
      </div>
    <?php else: ?>
      <p class="confirmation-text">Are you sure you want to delete the post titled:</p>
      <h4 class="text-center mb-4" style="color: var(--danger-color); font-weight: 700;">
        "<?= htmlspecialchars($post['title']) ?>"
      </h4>

      <form method="POST" novalidate>
        <input type="hidden" name="confirm_delete" value="1" />
        <div class="d-flex justify-content-center gap-3">
          <button type="submit" class="btn btn-danger btn-lg shadow-sm">Yes, Delete</button>
          <a href="<?= $returnToAdmin ? 'admin.php' : 'dashboard.php' ?>" class="btn btn-secondary btn-lg shadow-sm">Cancel</a>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

