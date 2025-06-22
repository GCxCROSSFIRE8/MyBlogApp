
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require 'db.php';

// Pagination setup
$postsPerPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $postsPerPage;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Count total posts
$countSql = "SELECT COUNT(*) as total FROM posts 
             JOIN users ON posts.user_id = users.id";

$dataSql = "SELECT posts.*, users.email_id AS email FROM posts 
            JOIN users ON posts.user_id = users.id";

// Apply search if keyword is present
if (!empty($search)) {
    $searchCondition = " WHERE posts.title LIKE '%$search%' OR posts.content LIKE '%$search%' OR users.email_id LIKE '%$search%'";
    $countSql .= $searchCondition;
    $dataSql .= $searchCondition;
}

// Order and limit
$dataSql .= " ORDER BY posts.created_at DESC LIMIT $offset, $postsPerPage";

// Get total count
$countResult = mysqli_query($conn, $countSql);
$totalPosts = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalPosts / $postsPerPage);

// Fetch paginated data
$result = mysqli_query($conn, $dataSql);
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
          <input type="text" name="search" class="form-control" placeholder="Search posts by title, content, or user email..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
      </form>

      <?php while ($post = mysqli_fetch_assoc($result)): ?>
        <div class="card mb-4 shadow-sm">
          <div class="card-body">
            <h4 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h4>
            <p class="card-text text-muted">
              by <?php echo htmlspecialchars($post['email']); ?> on <?php echo $post['created_at']; ?>
            </p>
            <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
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

    </div> <!-- col -->
  </div> <!-- row -->
</div> <!-- container -->


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html> 


