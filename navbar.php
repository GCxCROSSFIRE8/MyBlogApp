<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentFile = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard.php">📰 MyBlogApp</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
      aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
      <ul class="navbar-nav mb-2 mb-lg-0">

        <?php if (isset($_SESSION['user_id'])): ?>
          
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <li class="nav-item">
              <a 
                class="nav-link <?= $currentFile === 'admin.php' ? 'active' : ''; ?>" 
                href="admin.php"
              >
                🛠️ Admin Panel
              </a>
            </li>
          <?php endif; ?>

          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'dashboard.php' ? 'active' : ''; ?>" 
              href="dashboard.php"
            >
              🏠 Dashboard
            </a>
          </li>

          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'view.php' ? 'active' : ''; ?>" 
              href="view.php"
            >
              🌍 All Posts
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link text-danger" href="log-out.php">🚪 Log Out</a>
          </li>

        <?php else: ?>

          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'log-in.php' ? 'active' : ''; ?>" 
              href="log-in.php"
            >
              🔐 Login
            </a>
          </li>

          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'register.php' ? 'active' : ''; ?>" 
              href="register.php"
            >
              📝 Register
            </a>
          </li>

          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'view.php' ? 'active' : ''; ?>" 
              href="view.php"
            >
              🌍 Browse Posts
            </a>
          </li>

        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>




