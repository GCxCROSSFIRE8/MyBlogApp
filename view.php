<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

$perPage = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$search = trim($_GET['search'] ?? '');

try {
    // COUNT query
    if ($search !== '') {
        $sqlCount = "SELECT COUNT(*) FROM posts
                     JOIN users ON posts.user_id = users.id
                     WHERE posts.deleted_at IS NULL
                     AND (posts.title LIKE ? OR posts.content LIKE ? OR users.email LIKE ?)";
        $stmt = $conn->prepare($sqlCount);
        $searchTerm = "%$search%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM posts
                                JOIN users ON posts.user_id = users.id
                                WHERE posts.deleted_at IS NULL");
        $stmt->execute();
    }

    $total = (int)$stmt->fetchColumn();
    $totalPages = max(1, (int)ceil($total / $perPage));

    // DATA query
    if ($search !== '') {
        $sql = "SELECT posts.*, users.name AS author_name, users.email AS author_email
                FROM posts
                JOIN users ON posts.user_id = users.id
                WHERE posts.deleted_at IS NULL
                AND (posts.title LIKE ? OR posts.content LIKE ? OR users.email LIKE ?)
                ORDER BY posts.created_at DESC
                LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $offset, $perPage]);
    } else {
        $sql = "SELECT posts.*, users.name AS author_name, users.email AS author_email
                FROM posts
                JOIN users ON posts.user_id = users.id
                WHERE posts.deleted_at IS NULL
                ORDER BY posts.created_at DESC
                LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$offset, $perPage]);
    }

    $posts = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>⚠️ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>All Posts</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container mt-5">
  <h2>🌍 All Posts</h2>
  <form method="GET"><input type="text" name="search" class="form-control mb-3" placeholder="Search..." value="<?=htmlspecialchars($search)?>"></form>
  <?php if($posts): foreach($posts as $p): ?>
    <div class="card mb-3"><div class="card-body">
      <h4><?=htmlspecialchars($p['title'])?></h4>
      <small class="text-muted">by <?=htmlspecialchars($p['author_email'])?> on <?=$p['created_at']?></small>
      <p><?=nl2br(htmlspecialchars($p['content']))?></p>
    </div></div>
  <?php endforeach; else: ?>
    <div class="alert alert-info">No posts found.</div>
  <?php endif; ?>

  <?php if($totalPages>1): ?>
    <nav><ul class="pagination justify-content-center">
    <?php for($i=1;$i<=$totalPages;$i++): ?>
      <li class="page-item <?= $i==$page?'active':'' ?>">
        <a class="page-link" href="?page=<?=$i?><?= $search?'&search='.urlencode($search):'' ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
    </ul></nav>
  <?php endif; ?>
</div>
</body>
</html>





