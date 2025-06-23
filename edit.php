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
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id AND deleted_at IS NULL");
    $stmt->execute([':id' => $post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        $_SESSION['flash_error'] = "Post not found.";
        header("Location: dashboard.php");
        exit();
    }

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
            if ($isAdminEdit && strpos($content, '[Edited by Admin]') === false) {
                $content .= "\n\n[Edited by Admin]";
            }

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
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Post | MyBlogApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Background gradient and subtle animation */
        body.bg-light {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            animation: bgPulse 15s ease-in-out infinite;
        }
        @keyframes bgPulse {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Container styling */
        .container.mt-4 {
            max-width: 720px;
            background: #ffffffdd;
            padding: 2.5rem 3rem;
            border-radius: 1rem;
            box-shadow:
                0 4px 8px rgba(0,0,0,0.1),
                0 8px 20px rgba(0,0,0,0.08);
            transition: box-shadow 0.3s ease;
        }
        .container.mt-4:hover {
            box-shadow:
                0 8px 20px rgba(0,0,0,0.15),
                0 16px 40px rgba(0,0,0,0.12);
        }

        /* Heading with fade-in text animation */
        h2 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        h2::after {
            content: "✏️";
            position: absolute;
            left: -3rem;
            animation: slideIn 1.5s forwards;
            font-size: 2.8rem;
            top: 50%;
            transform: translateY(-50%);
        }
        @keyframes slideIn {
            to { left: 1rem; }
        }

        /* Form labels */
        label.form-label {
            font-weight: 600;
            color: #334155;
            transition: color 0.3s ease;
        }
        input.form-control:focus,
        textarea.form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 8px rgba(59,130,246,0.5);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Inputs and textareas */
        input.form-control,
        textarea.form-control {
            border-radius: 0.5rem;
            box-shadow: inset 0 2px 6px rgba(0,0,0,0.07);
            font-size: 1.1rem;
            padding: 0.8rem 1rem;
            transition: box-shadow 0.3s ease;
        }
        input.form-control:hover,
        textarea.form-control:hover {
            box-shadow: inset 0 3px 8px rgba(0,0,0,0.1);
        }

        /* Buttons with hover animation */
        button.btn-primary {
            background-color: #2563eb;
            border: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 0.6rem;
            box-shadow: 0 5px 12px rgba(37, 99, 235, 0.4);
            transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
        }
        button.btn-primary:hover {
            background-color: #1e40af;
            box-shadow: 0 8px 18px rgba(30, 64, 175, 0.6);
            transform: translateY(-2px);
        }
        button.btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 5px 12px rgba(37, 99, 235, 0.4);
        }

        /* Cancel button */
        a.btn-secondary {
            background-color: #64748b;
            border: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 0.6rem;
            box-shadow: 0 3px 10px rgba(100, 116, 139, 0.3);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        a.btn-secondary:hover {
            background-color: #475569;
            box-shadow: 0 5px 15px rgba(71, 85, 105, 0.45);
            text-decoration: none;
            color: #e2e8f0;
        }

        /* Alerts fade in */
        .alert {
            animation: fadeInAlert 0.8s ease forwards;
        }
        @keyframes fadeInAlert {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>

<div class="container mt-4 shadow-sm">
    <h2>Edit Post</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-4">
            <label class="form-label" for="title">Title</label>
            <input id="title" type="text" name="title" class="form-control" required value="<?= htmlspecialchars($post['title'] ?? '') ?>" autocomplete="off" />
        </div>

        <div class="mb-4">
            <label class="form-label" for="content">Content</label>
            <textarea id="content" name="content" class="form-control" rows="8" required><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Post</button>
        <a href="<?= $isAdminEdit ? 'admin.php' : 'dashboard.php' ?>" class="btn btn-secondary ms-3">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

