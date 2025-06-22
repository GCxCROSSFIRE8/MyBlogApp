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
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container mt-5" style="max-width:400px;">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h3 class="text-center mb-4">Create Account</h3>
      <form method="POST">
        <div class="mb-3">
          <input class="form-control form-control-lg" type="email" name="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input class="form-control form-control-lg" type="password" name="password" placeholder="Password" required>
        </div>
        <button class="btn btn-success btn-lg w-100">Register</button>
      </form>
      <p class="mt-3 text-center">
        Already registered? <a href="log-in.php">Log In</a>
      </p>
      <p class="text-center">
        <a href="view.php">Browse Posts</a>
      </p>
    </div>
  </div>
</div>

    </form>
</div>
<!-- <script src="include/js/bootstrap.bundle.js"></script> -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>