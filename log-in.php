<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['email']    = $user['email'];
                $_SESSION['username'] = $user['name'] ?? '';
                $_SESSION['role']     = $user['role'] ?? 'user';
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "❌ Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "⚠️ Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login - MyBlogApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #f59e0b;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --error-color: #dc3545;
            --success-color: #28a745;
            --input-border: #ced4da;
            --input-focus: var(--primary-color);
            --text-color: #1f2937;
        }
        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgb(79 70 229 / 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: var(--card-bg);
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgb(79 70 229 / 0.35);
        }
        h3 {
            color: var(--primary-color);
            font-weight: 700;
            user-select: none;
            animation: fadeInSlideDown 0.8s ease forwards;
            opacity: 0;
            transform: translateY(-20px);
        }
        @keyframes fadeInSlideDown {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        form {
            animation: fadeInSlideUp 0.8s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        @keyframes fadeInSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .form-control {
            border: 2px solid var(--input-border);
            border-radius: 0.5rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-size: 1.1rem;
            color: var(--text-color);
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 8px var(--primary-color);
            outline: none;
        }
        .btn-primary {
            background: var(--primary-color);
            border: none;
            font-weight: 700;
            font-size: 1.2rem;
            padding: 0.75rem;
            border-radius: 0.6rem;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 12px rgb(79 70 229 / 0.3);
            user-select: none;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--secondary-color);
            box-shadow: 0 6px 18px rgb(245 158 11 / 0.6);
            outline: none;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: var(--error-color);
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            text-align: center;
            box-shadow: 0 4px 15px rgb(220 53 69 / 0.25);
            user-select: none;
        }
        .alert-success {
            background-color: #d4edda;
            color: var(--success-color);
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            text-align: center;
            box-shadow: 0 4px 15px rgb(40 167 69 / 0.3);
            user-select: none;
        }
        .text-center a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }
        .text-center a:hover {
            color: var(--secondary-color);
            text-shadow: 0 0 8px var(--secondary-color);
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-sm p-5" style="width: 100%; max-width: 420px;">
        <h3 class="text-center mb-4">🔐 Login to MyBlogApp</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4">
                <input type="email" name="email" class="form-control form-control-lg" required placeholder="you@example.com" autofocus>
            </div>
            <div class="mb-4">
                <input type="password" name="password" class="form-control form-control-lg" required placeholder="Your password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="mt-4 text-center">
            Not registered yet? <a href="register.php">Create an account</a><br />
            <a href="view.php">🌍 Browse posts</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


