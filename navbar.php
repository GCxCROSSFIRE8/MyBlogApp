<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
          <li class="nav-item">
            <a 
              class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" 
              aria-current="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'page' : ''; ?>" 
              href="dashboard.php"
            >
              🏠 Dashboard
            </a>
          </li>

          <li class="nav-item">
            <a 
              class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'view.php' ? 'active' : ''; ?>" 
              aria-current="<?php echo basename($_SERVER['PHP_SELF']) === 'view.php' ? 'page' : ''; ?>" 
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
              class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'log-in.php' ? 'active' : ''; ?>" 
              aria-current="<?php echo basename($_SERVER['PHP_SELF']) === 'log-in.php' ? 'page' : ''; ?>" 
              href="log-in.php"
            >
              🔐 Login
            </a>
          </li>

          <li class="nav-item">
            <a 
              class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'register.php' ? 'active' : ''; ?>" 
              aria-current="<?php echo basename($_SERVER['PHP_SELF']) === 'register.php' ? 'page' : ''; ?>" 
              href="register.php"
            >
              📝 Register
            </a>
          </li>

          <li class="nav-item">
            <a 
              class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'view.php' ? 'active' : ''; ?>" 
              aria-current="<?php echo basename($_SERVER['PHP_SELF']) === 'view.php' ? 'page' : ''; ?>" 
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



