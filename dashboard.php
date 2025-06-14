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
        echo "Post created successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

$sql = "SELECT * FROM posts WHERE user_id = " . $_SESSION['user_id'];
$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="include/css/bootstrap.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="mb-4">
        <h2>👋 Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    </div>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div class="alert alert-success">✅ Post deleted successfully!</div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">📝 Create New Post</h4>
            <form method="POST">
                <input class="form-control mb-2" type="text" name="title" placeholder="Post Title" required>
                <textarea class="form-control mb-2" name="content" placeholder="Post Content" rows="4" required></textarea>
                <button class="btn btn-success">Publish</button>
            </form>
        </div>
    </div>

<!-- <div class="container mt-5">

<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
    <div class="alert alert-success" role="alert">
        ✅ Post deleted successfully!
    </div>
<?php endif; ?>


<h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>

<h3>Create New Post</h3>
<form method="POST">
    <input type="text" name="title" placeholder="Post Title" required>
    <textarea name="content" placeholder="Post Content" required></textarea>
    <button type="submit">Publish</button>
</form> -->

<h3 class="mb-3">📚 Your Posts</h3>
<?php while ($post = mysqli_fetch_assoc($result)): ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
            <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?php echo $post['id']; ?>">Edit</a>
            <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?php echo $post['id']; ?>">Delete</a>
        </div>
    </div>
<?php endwhile; ?>


</div>

</div>
<script src="include/js/bootstrap.bundle.js"></script>

</body>
</html>
