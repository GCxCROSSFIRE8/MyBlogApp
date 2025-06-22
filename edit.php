<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM posts WHERE id = '$id' AND user_id = " . $_SESSION['user_id'];
    $result = mysqli_query($conn, $sql);
    $post = mysqli_fetch_assoc($result);

    if (!$post) {
        echo "Post not found!";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);

        $sql = "UPDATE posts SET title = '$title', content = '$content' WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
} else {
    echo "Invalid post ID!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Post</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">
<!-- <div class="container mt-5">


<h3>Edit Post</h3>
<form method="POST">
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
    <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    <button type="submit">Update</button>
</form>

</div> -->

<div class="container mt-5">
    <h3>Edit Post</h3>
    <form method="POST">
        <div class="mb-3">
            <input class="form-control" type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        <div class="mb-3">
            <textarea class="form-control" name="content" rows="6" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Update</button>
    </form>
</div>
<!-- <script src="include/js/bootstrap.bundle.js"></script> -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
