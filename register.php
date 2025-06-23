<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Server-side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "❌ Password must be at least 6 characters.";
    } else {
        // Check for existing user
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $checkStmt->execute(['email' => $email]);

        if ($checkStmt->rowCount() > 0) {
            $error = "❌ Email already registered.";
        } else {
            // Register user with hashed password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; // default role

            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (:email, :password, :role)");
            $stmt->execute([
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role
            ]);

            $success = "✅ Registration successful! You can now log in.";
        }
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

      <?php if (isset($error)): ?>
          <div class="alert alert-danger text-center"><?= $error ?></div>
      <?php elseif (isset($success)): ?>
          <div class="alert alert-success text-center"><?= $success ?></div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="mb-3">
          <input class="form-control form-control-lg" type="email" name="email" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input class="form-control form-control-lg" type="password" name="password" placeholder="Password (min 6 chars)" required>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
