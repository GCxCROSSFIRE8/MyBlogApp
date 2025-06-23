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
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Create Account - MyBlogApp</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  /* Reuse same palette */
  :root {
    --primary-color: #4f46e5;
    --secondary-color: #f59e0b;
    --bg-light: #f9fafb;
    --text-primary: #111827;
    --text-secondary: #6b7280;
  }

  body {
    background-color: var(--bg-light);
    color: var(--text-primary);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  h3 {
    font-weight: 700;
    font-size: 2rem;
    text-align: center;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    position: relative;
    overflow: hidden;
  }

  /* Animated heading gradient */
  h3::after {
    content: 'Create Account';
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, var(--secondary-color), transparent);
    animation: slideGradient 3s linear infinite;
    mix-blend-mode: screen;
  }

  @keyframes slideGradient {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
  }

  .card {
    border-radius: 1rem;
    box-shadow:
      0 8px 16px rgba(79, 70, 229, 0.15),
      0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .form-control {
    border: 2px solid var(--primary-color);
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }
  .form-control:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 10px var(--secondary-color);
    outline: none;
  }

  button.btn-success {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    font-weight: 600;
    font-size: 1.25rem;
    padding: 0.75rem;
    border-radius: 0.75rem;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }
  button.btn-success:hover {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
    box-shadow: 0 0 15px var(--secondary-color);
  }

  .alert {
    font-weight: 600;
    border-radius: 0.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
  }

  p.mt-3, p.text-center {
    font-size: 1rem;
  }

  p a {
    color: var(--primary-color);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
  }
  p a:hover {
    color: var(--secondary-color);
    text-decoration: underline;
  }

  .container {
    max-width: 400px;
  }

  @media (max-width: 576px) {
    h3 {
      font-size: 1.75rem;
    }
  }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
  <div class="card shadow-sm p-4">
    <h3>Create Account</h3>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif (isset($success)): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <input 
          type="email" 
          name="email" 
          placeholder="Email" 
          class="form-control" 
          required 
          autocomplete="email"
          aria-label="Email address"
        >
      </div>
      <div class="mb-3">
        <input 
          type="password" 
          name="password" 
          placeholder="Password (min 6 chars)" 
          class="form-control" 
          required 
          minlength="6"
          autocomplete="new-password"
          aria-label="Password"
        >
      </div>
      <button class="btn btn-success w-100" type="submit">Register</button>
    </form>

    <p class="mt-3 text-center">
      Already registered? <a href="log-in.php">Log In</a>
    </p>
    <p class="text-center">
      <a href="view.php">Browse Posts</a>
    </p>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

