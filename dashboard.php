<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO posts (user_id, title, content) VALUES ('$user_id', '$title', '$content')";
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success text-center'>Post created successfully!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Pagination setup
$postsPerPage = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $postsPerPage;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$userId = $_SESSION['user_id'];

// Build query for count and data
$countSql = "SELECT COUNT(*) as total FROM posts WHERE user_id = $userId";
$dataSql = "SELECT * FROM posts WHERE user_id = $userId";

if (!empty($search)) {
    $countSql .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
    $dataSql .= " AND (title LIKE '%$search%' OR content LIKE '%$search%')";
}

$dataSql .= " ORDER BY created_at DESC LIMIT $offset, $postsPerPage";

// Get total posts
$countResult = mysqli_query($conn, $countSql);
$totalPosts = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalPosts / $postsPerPage);

// Get paginated data
$result = mysqli_query($conn, $dataSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-5 mb-5">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2>👋 Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <div>
      <a href="view.php" class="btn btn-secondary me-2 mb-2">View All Posts</a>
      <a href="/myblogapp/log-out.php" class="btn btn-danger mb-2">Log Out</a>
    </div>
  </div>

  <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
      <div class="alert alert-success">✅ Post deleted successfully!</div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4 rounded">
      <div class="card-body">
          <h4 class="card-title mb-3">📝 Create New Post</h4>
          <form method="POST">
              <input class="form-control mb-3" type="text" name="title" placeholder="Post Title" required />
              <textarea class="form-control mb-3" name="content" placeholder="Post Content" rows="5" required></textarea>
              <button type="submit" class="btn btn-success w-100">Publish</button>
          </form>
      </div>
  </div>

  <form method="GET" class="mb-4">
    <input
      type="text"
      name="search"
      class="form-control"
      placeholder="Search posts by title or content..."
      value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
    />
  </form>

  <h3 class="mb-3">📚 Your Posts</h3>

  <?php while ($post = mysqli_fetch_assoc($result)): ?>
      <div class="card mb-3 shadow-sm rounded">
          <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
              <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
              <div>
                <a class="btn btn-sm btn-outline-primary me-2" href="edit.php?id=<?php echo $post['id']; ?>">Edit</a>
                <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
              </div>
          </div>
      </div>
  <?php endwhile; ?>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

