<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

$postsPerPage = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $postsPerPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$countSql = "SELECT COUNT(*) FROM posts WHERE deleted_at IS NULL";
$dataSql = "SELECT posts.*, users.name AS author_name, users.email_id AS author_email
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.deleted_at IS NULL";

$params = [];

if (!empty($search)) {
    $countSql .= " AND (posts.title LIKE :search OR posts.content LIKE :search OR users.email_id LIKE :search)";
    $dataSql .= " AND (posts.title LIKE :search OR posts.content LIKE :search OR users.email_id LIKE :search)";
    $params['search'] = "%$search%";
}

$dataSql .= " ORDER BY posts.created_at DESC LIMIT :offset, :limit";

try {
    // Count total posts
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute($params);
    $totalPosts = $countStmt->fetchColumn();

    $totalPages = ceil($totalPosts / $postsPerPage);

    // Fetch posts with pagination
    $dataStmt = $conn->prepare($dataSql);

    if (!empty($search)) {
        $dataStmt->bindValue(':search', $params['search'], PDO::PARAM_STR);
    }
    $dataStmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $dataStmt->bindValue(':limit', (int)$postsPerPage, PDO::PARAM_INT);

    $dataStmt->execute();
    $posts = $dataStmt->fetchAll();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger text-center'>⚠️ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <h2 class="mb-4">🌍 All Posts</h2>

            <form method="GET" class="mb-4">
                <input type="text" name="search" class="form-control" placeholder="Search posts by title, content, or user email..." value="<?php echo htmlspecialchars($search); ?>">
            </form>

            <?php if ($posts): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h4>
                            <p class="card-text text-muted">
                              by <?php echo htmlspecialchars($post['author_email']); ?> on <?php echo $post['created_at']; ?>
 
                            </p>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">No posts found.</div>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <nav>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>


