<?php
require 'db.php';

// $sql = "SELECT posts.*, users.name FROM posts JOIN users ON posts.user_id = users.id
// ORDER BY posts.created_at DESC";
$sql = "SELECT posts.*, users.email FROM posts JOIN users ON posts.user_id = users.id
ORDER BY posts.created_at DESC";

$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html>
<head>
    <title>All Posts</title>
    <link rel="stylesheet" href="include/css/bootstrap.css">
</head>
<body class="bg-light">

<!-- <p>by <?php echo htmlspecialchars($post['email']); ?> on <?php echo $post['created_at']; ?></p> -->


<!-- <h2>All Posts</h2>
<?php while ($post = mysqli_fetch_assoc($result)): ?>
    <div>
        <h4><?php echo htmlspecialchars($post['title']); ?></h4>
        <p>by <?php echo htmlspecialchars($post['name']); ?> on <?php echo $post['created_at']; ?></p>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
    </div>
<?php endwhile; ?> -->


<div class="container mt-5">
    <h2 class="mb-4">All Posts</h2>
    <?php while ($post = mysqli_fetch_assoc($result)): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h4>
                <h6 class="card-subtitle mb-2 text-muted">
                    by <?php echo htmlspecialchars($post['email']); ?> on <?php echo $post['created_at']; ?>
                </h6>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script src="include/js/bootstrap.bundle.js"></script>
</body>
</html>