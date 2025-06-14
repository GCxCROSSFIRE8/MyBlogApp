<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (email_id, password) VALUES ('$email', '$password')";
    if (mysqli_query($conn, $sql)) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="include/css/bootstrap.css">
</head>
<body>

<!-- <form method="POST">
    <input type="email" name="email" placeholder="email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form> -->
<div class="container mt-5">
    <h2>Register</h2>
    <form method="POST">
        <div class="mb-3">
            <input class="form-control" type="email" name="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input class="form-control" type="password" name="password" placeholder="Password" required>
        </div>
        <button class="btn btn-primary" type="submit">Register</button>
    </form>
</div>
<script src="include/js/bootstrap.bundle.js"></script>
</body>
</html>