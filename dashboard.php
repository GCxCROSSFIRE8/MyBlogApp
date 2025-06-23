<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log-in.php");
    exit();
}

// Flash messages
if (isset($_SESSION['flash_success'])) {
    echo "<div class='alert alert-success text-center flash-msg'>" . htmlspecialchars($_SESSION['flash_success']) . "</div>";
    unset($_SESSION['flash_success']);
}

if (isset($_SESSION['flash_error'])) {
    echo "<div class='alert alert-danger text-center flash-msg'>" . htmlspecialchars($_SESSION['flash_error']) . "</div>";
    unset($_SESSION['flash_error']);
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'user';

// Handle post creation
$createMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (!empty($title) && !empty($content)) {
        try {
            $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (:user_id, :title, :content)");
            $stmt->execute([
                ':user_id' => $userId,
                ':title'   => $title,
                ':content' => $content
            ]);
            $createMessage = "<div class='alert alert-success text-center flash-msg'>✅ Post created successfully!</div>";
        } catch (PDOException $e) {
            $createMessage = "<div class='alert alert-danger text-center flash-msg'>⚠️ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $createMessage = "<div class='alert alert-danger text-center flash-msg'>⚠️ Title and Content are required.</div>";
    }
}

// Pagination + Search
$postsPerPage = 4;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $postsPerPage;
$search = trim($_GET['search'] ?? '');

$countSql = "SELECT COUNT(*) FROM posts WHERE user_id = :user_id";
$dataSql  = "SELECT * FROM posts WHERE user_id = :user_id";
$params   = [':user_id' => $userId];

if ($search !== '') {
    $countSql .= " AND (title LIKE :search OR content LIKE :search)";
    $dataSql  .= " AND (title LIKE :search OR content LIKE :search)";
    $params[':search'] = "%$search%";
}

$dataSql .= " ORDER BY created_at DESC LIMIT :offset, :limit";

try {
    $countStmt = $conn->prepare($countSql);
    $dataStmt  = $conn->prepare($dataSql);

    $countStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $dataStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

    if ($search !== '') {
        $countStmt->bindValue(':search', $params[':search'], PDO::PARAM_STR);
        $dataStmt->bindValue(':search', $params[':search'], PDO::PARAM_STR);
    }

    $countStmt->execute();
    $totalPosts = $countStmt->fetchColumn();
    $totalPages = ceil($totalPosts / $postsPerPage);

    $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $dataStmt->bindValue(':limit', $postsPerPage, PDO::PARAM_INT);
    $dataStmt->execute();
    $posts = $dataStmt->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger flash-msg'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard | Your Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
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
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        body {
            background: #f9fafb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        h2 {
            animation: fadeInDown 0.8s ease forwards;
            color: #2c3e50;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            letter-spacing: 1px;
        }

        .flash-msg {
            animation: fadeInDown 0.7s ease forwards;
            max-width: 600px;
            margin: 0.5rem auto;
            font-weight: 600;
            box-shadow: 0 0 12px rgb(0 0 0 / 0.15);
            border-radius: 8px;
        }

        .card {
            border-radius: 12px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgb(0 0 0 / 0.15);
        }

        .card-header {
            background: linear-gradient(45deg, #00b4db, #0083b0);
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            text-align: center;
            letter-spacing: 0.8px;
            user-select: none;
            box-shadow: 0 3px 10px rgb(0 0 0 / 0.1);
        }

        .btn-primary {
            background: linear-gradient(45deg, #0062E6, #33AEFF);
            border: none;
            font-weight: 600;
            transition: background 0.3s ease;
            box-shadow: 0 5px 10px rgb(51 174 255 / 0.5);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(45deg, #33AEFF, #0062E6);
            box-shadow: 0 8px 15px rgb(51 174 255 / 0.75);
        }

        /* Search box with icon */
        form.mb-4 {
            max-width: 600px;
            margin: 0 auto 2rem;
            position: relative;
        }
        form.mb-4 input[type="text"] {
            padding-left: 40px;
            border-radius: 50px;
            border: 1.8px solid #0083b0;
            transition: border-color 0.3s ease;
            font-size: 1.1rem;
        }
        form.mb-4 input[type="text"]:focus {
            border-color: #00b4db;
            box-shadow: 0 0 8px #00b4dbaa;
            outline: none;
        }
        form.mb-4::before {
            content: '🔍';
            position: absolute;
            left: 15px;
            top: 10px;
            font-size: 1.3rem;
            color: #0083b0;
            user-select: none;
        }

        .card-title {
            font-weight: 700;
            color: #34495e;
            transition: color 0.3s ease;
        }
        .card:hover .card-title {
            color: #00b4db;
        }

        .btn-outline-primary {
            border-radius: 30px;
            padding-left: 1.3rem;
            padding-right: 1.3rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgb(0 132 176 / 0.3);
        }
        .btn-outline-primary:hover {
            background: #00b4db;
            color: white;
            box-shadow: 0 5px 15px rgb(0 132 176 / 0.6);
            transform: scale(1.05);
        }

        .btn-outline-danger {
            border-radius: 30px;
            padding-left: 1.3rem;
            padding-right: 1.3rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgb(220 53 69 / 0.3);
        }
        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
            box-shadow: 0 5px 15px rgb(220 53 69 / 0.6);
            transform: scale(1.05);
        }

        small.text-muted {
            font-style: italic;
            color: #6c757d;
        }

        /* Pagination */
        .pagination {
            user-select: none;
        }
        .page-item.active .page-link {
            background: #00b4db;
            border-color: #00b4db;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 10px rgb(0 180 219 / 0.6);
            transition: background 0.3s ease;
        }
        .page-link {
            border-radius: 8px;
            color: #0083b0;
            font-weight: 600;
            transition: color 0.3s ease, background 0.3s ease;
        }
        .page-link:hover {
            background: #00b4db;
            color: white;
            box-shadow: 0 5px 15px rgb(0 180 219 / 0.7);
        }

        /* Disabled delete alert animation */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25%, 75% { transform: translateX(-8px); }
            50% { transform: translateX(8px); }
        }
    </style>
    <script>
        // Alert when delete is disabled for regular users
        function confirmDelete() {
            const alertBox = document.createElement('div');
            alertBox.textContent = "❌ You don't have permission to delete this post.";
            alertBox.className = "alert alert-danger flash-msg";
            alertBox.style.position = "fixed";
            alertBox.style.top = "20px";
            alertBox.style.left = "50%";
            alertBox.style.transform = "translateX(-50%)";
            alertBox.style.zIndex = "1050";
            alertBox.style.animation = "shake 0.5s ease";
            document.body.appendChild(alertBox);
            setTimeout(() => alertBox.remove(), 3000);
            return false;
        }
    </script>
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2>📋 Your Dashboard</h2>
    <?= $createMessage ?>

    <!-- Create Post Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">✍️ Create a New Post</div>
        <div class="card-body">
            <form method="POST" autocomplete="off">
                <input
                    type="text"
                    name="title"
                    class="form-control mb-3"
                    placeholder="Post Title"
                    required
                    maxlength="100"
                />
                <textarea
                    name="content"
                    class="form-control mb-3"
                    rows="5"
                    placeholder="Write your post content here..."
                    required
                    maxlength="2000"
                ></textarea>
                <button class="btn btn-primary w-100" type="submit">Create Post</button>
            </form>
        </div>
    </div>

    <!-- Search Bar -->
    <form method="GET" class="mb-4" role="search" aria-label="Search posts">
        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Search your posts..."
            value="<?= htmlspecialchars($search) ?>"
            aria-describedby="searchHelp"
        />
    </form>

    <!-- Posts List -->
    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    <small class="text-muted">Created at: <?= $post['created_at'] ?></small>
                    <div class="mt-3">
                        <a href="edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary me-2" aria-label="Edit post titled <?= htmlspecialchars($post['title']) ?>">Edit</a>

                        <?php if (in_array($userRole, ['admin', 'editor'])): ?>
                            <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post?');" aria-label="Delete post titled <?= htmlspecialchars($post['title']) ?>">Delete</a>
                        <?php else: ?>
                            <a href="#" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete()" aria-label="Delete post disabled">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info text-center" role="alert">No posts found. Try creating one!</div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

