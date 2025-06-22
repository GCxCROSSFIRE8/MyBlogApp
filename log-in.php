<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email_id = '$email'";

    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);


// Debug print
echo "<pre>";
print_r($user);
echo "</pre>";

if ($user) {
    echo "<br>Stored hash: " . $user['password']; // <-- show stored hash
    echo "<br>Entered password: " . $password;    // <-- show user input

    if (password_verify($password, $user['password'])) {
        echo "<br>Password matched ✅";
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email_id'];
        $_SESSION['username'] = $user['name'];  // ✅ add this line
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<br>Password didn't match ❌";
    }
} else {
    echo "<br>No user found ❌";
}
}

?>

<!DOCTYPE html>
<html>
<head>
   
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4">🔐 Login to MyBlogApp</h3>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required placeholder="you@example.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Your password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="mt-3 text-center">
            Not registered yet? <a href="register.php">Create an account</a><br>
            <a href="view.php">🌍 Want to browse more posts?</a>
        </p>
    </div>
</div>


    </form>
</div>
<!-- <script src="include/js/bootstrap.bundle.js"></script> -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>