<?php
session_start();
require 'db.php';

// Admin access only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: log-in.php');
    exit();
}

// Flash messages
$flashSuccess = $_SESSION['flash_success'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Fetch all non-deleted posts from all users
try {
    $stmt = $conn->prepare("
        SELECT posts.*, users.email AS author_email
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.deleted_at IS NULL
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    die("<div class='alert alert-danger text-center mt-5'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Panel - Manage All Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 700;
            color: #2c3e50;
            letter-spacing: 1px;
            animation: fadeInDown 0.7s ease forwards;
        }
        .alert {
            max-width: 700px;
            margin: 0.5rem auto;
            font-weight: 600;
            box-shadow: 0 0 10px rgb(0 0 0 / 0.1);
            border-radius: 8px;
            animation: fadeInDown 0.6s ease forwards;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 7px rgb(0 0 0 / 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 20px rgb(0 0 0 / 0.15);
        }
        .card-title {
            font-weight: 700;
            color: #34495e;
            transition: color 0.3s ease;
        }
        .card:hover .card-title {
            color: #007bff;
        }
        .author-info {
            font-weight: 600;
            color: #495057;
        }
        .text-muted {
            font-style: italic;
        }
        .btn-outline-primary, .btn-outline-danger {
            border-radius: 30px;
            font-weight: 600;
            padding-left: 1.2rem;
            padding-right: 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgb(0 123 255 / 0.3);
        }
        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
            box-shadow: 0 6px 18px rgb(0 123 255 / 0.6);
            transform: scale(1.05);
        }
        .btn-outline-danger {
            box-shadow: 0 3px 10px rgb(220 53 69 / 0.3);
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
            box-shadow: 0 6px 18px rgb(220 53 69 / 0.6);
            transform: scale(1.05);
        }
        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5 mb-5">
    <h2>🛠️ Admin Panel: Manage All Posts</h2>

    <?php if ($flashSuccess): ?>
        <div class="alert alert-success text-center" role="alert"><?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="alert alert-danger text-center" role="alert"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>

    <?php if (!$posts): ?>
        <div class="alert alert-info text-center" role="alert">ℹ️ No posts available.</div>
    <?php else: ?>
        <?php foreach ($posts as $p): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($p['title']) ?></h5>
                    <p class="author-info mb-1"><strong>Author:</strong> <?= htmlspecialchars($p['author_email']) ?></p>
                    <p class="text-muted mb-2">
                        Created: <?= htmlspecialchars($p['created_at']) ?>
                        <?php if ($p['updated_at']): ?>
                            | Updated: <?= htmlspecialchars($p['updated_at']) ?>
                        <?php endif; ?>
                    </p>
                    <p><?= nl2br(htmlspecialchars($p['content'])) ?></p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="edit.php?id=<?= $p['id'] ?>&admin=1" 
                           class="btn btn-sm btn-outline-primary" 
                           aria-label="Edit post titled <?= htmlspecialchars($p['title']) ?>">
                           ✏️ Edit
                        </a>
                        <a href="delete.php?id=<?= $p['id'] ?>&admin=1" 
                           class="btn btn-sm btn-outline-danger" 
                           onclick="return confirm('Are you sure you want to delete this post?')" 
                           aria-label="Delete post titled <?= htmlspecialchars($p['title']) ?>">
                           🗑️ Delete
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



