<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (isset($_SESSION['flash_success'])) {
    echo "<div class='alert alert-success text-center'>" . htmlspecialchars($_SESSION['flash_success']) . "</div>";
    unset($_SESSION['flash_success']);
}

if (isset($_SESSION['flash_error'])) {
    echo "<div class='alert alert-danger text-center'>" . htmlspecialchars($_SESSION['flash_error']) . "</div>";
    unset($_SESSION['flash_error']);
}

require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log-in.php");
    exit();
}

// Get user role
$userRole = $_SESSION['role'] ?? 'user';

// Handle post creation - only admin/editor and normal user can create
$createMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (in_array($userRole, ['admin', 'editor', 'user'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $user_id = $_SESSION['user_id'];

        if (!empty($title) && !empty($content)) {
            try {
                $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (:user_id, :title, :content)");
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':title' => $title,
                    ':content' => $content
                ]);
                $createMessage = "<div class='alert alert-success text-center'>✅ Post created successfully!</div>";
            } catch (PDOException $e) {
                $createMessage = "<div class='alert alert-danger text-center'>⚠️ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            $createMessage = "<div class='alert alert-danger text-center'>⚠️ Title and Content are required.</div>";
        }
    } else {
        $createMessage = "<div class='alert alert-danger text-center'>❌ You do not have permission to create posts.</div>";
    }
}

// Pagination + search
$postsPerPage = 4;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $postsPerPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$userId = $_SESSION['user_id'];

$countSql = "SELECT COUNT(*) FROM posts WHERE user_id = :user_id";
$dataSql = "SELECT * FROM posts WHERE user_id = :user_id";

$params = [];

if (!empty($search)) {
    $countSql .= " AND (title LIKE :search1 OR content LIKE :search2)";
    $dataSql .= " AND (title LIKE :search1 OR content LIKE :search2)";
    $params['search1'] = "%$search%";
    $params['search2'] = "%$search%";
}

$dataSql .= " ORDER BY created_at DESC LIMIT :offset, :limit";

try {
    $countStmt = $conn->prepare($countSql);
    $dataStmt = $conn->prepare($dataSql);

    $countStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $dataStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

    if (!empty($search)) {
        $countStmt->bindValue(':search1', $params['search1'], PDO::PARAM_STR);
        $countStmt->bindValue(':search2', $params['search2'], PDO::PARAM_STR);

        $dataStmt->bindValue(':search1', $params['search1'], PDO::PARAM_STR);
        $dataStmt->bindValue(':search2', $params['search2'], PDO::PARAM_STR);
    }

    $countStmt->execute();
    $totalPosts = $countStmt->fetchColumn();
    $totalPages = ceil($totalPosts / $postsPerPage);

    $dataStmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $dataStmt->bindValue(':limit', (int)$postsPerPage, PDO::PARAM_INT);
    $dataStmt->execute();
    $posts = $dataStmt->fetchAll();

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmDelete() {
            alert("You can't delete this post.");
            return false;
        }
    </script>
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Your Dashboard</h2>

    <!-- Flash message -->
    <?php echo $createMessage; ?>

    <!-- Post creation form (for all users) -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">✍️ Create a New Post</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="title" class="form-control" placeholder="Post Title" required>
                </div>
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="5" placeholder="Post Content..." required></textarea>
                </div>
                <button class="btn btn-primary">Create Post</button>
            </form>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" class="mb-4">
        <input type="text" name="search" class="form-control" placeholder="Search your posts..." value="<?php echo htmlspecialchars($search); ?>">
    </form>

    <!-- Post list -->
    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    <small class="text-muted">Created at: <?php echo $post['created_at']; ?></small><br>

                    <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary mt-2">Edit</a>

                    <?php if ($userRole === 'admin' || $userRole === 'editor'): ?>
                        <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-danger mt-2" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php else: ?>
                        <a href="#" class="btn btn-sm btn-outline-danger mt-2" onclick="return confirmDelete()">Delete</a>
                    <?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">No posts found.</div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
