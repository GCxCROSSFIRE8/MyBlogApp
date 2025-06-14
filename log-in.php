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
    <link rel="stylesheet" href="include/css/bootstrap.css">
</head>
<body>

<div class="container mt-5">
    <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-4">Welcome Back 👋</h3>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Email</label>
                            <input class="form-control" type="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input class="form-control" type="password" name="password" required>
                        </div>
                        <button class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- <h2 class="text-center">Login</h2>
    <form method="POST" class="p-4 border rounded shadow" style="max-width: 400px; margin: auto;">
        <div class="mb-3">
            <input class="form-control" type="email" name="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input class="form-control" type="password" name="password" placeholder="Password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Login</button> -->
    </form>
</div>
<script src="include/js/bootstrap.bundle.js"></script>
</body>
</html>