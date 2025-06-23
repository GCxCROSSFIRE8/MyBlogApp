<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentFile = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <style>
    :root {
      --primary-color: #4f46e5; /* Indigo */
      --secondary-color: #f59e0b; /* Amber */
      --navbar-bg: rgba(31, 41, 55, 0.9); /* Dark slate with transparency */
      --navbar-shadow: rgba(79, 70, 229, 0.4);
      --link-color: #d1d5db; /* light gray */
      --link-hover-color: var(--secondary-color);
      --active-color: var(--secondary-color);
    }

    nav.navbar {
      background-color: var(--navbar-bg);
      backdrop-filter: saturate(180%) blur(12px);
      box-shadow: 0 2px 10px var(--navbar-shadow);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      z-index: 9999;
    }

    nav.navbar a.navbar-brand {
      font-weight: 800;
      font-size: 1.5rem;
      color: var(--primary-color);
      transition: transform 0.3s ease, color 0.3s ease;
      user-select: none;
    }

    nav.navbar a.navbar-brand:hover {
      color: var(--secondary-color);
      transform: scale(1.1);
      text-shadow: 0 0 10px var(--secondary-color);
    }

    .navbar-nav .nav-link {
      color: var(--link-color);
      font-weight: 600;
      position: relative;
      padding: 0.5rem 1rem;
      transition: color 0.3s ease;
    }

    .navbar-nav .nav-link::after {
      content: '';
      position: absolute;
      left: 50%;
      bottom: 0;
      transform: translateX(-50%) scaleX(0);
      transform-origin: center;
      width: 60%;
      height: 2px;
      background-color: var(--secondary-color);
      transition: transform 0.3s ease;
      border-radius: 1px;
    }

    .navbar-nav .nav-link:hover {
      color: var(--secondary-color);
    }

    .navbar-nav .nav-link:hover::after {
      transform: translateX(-50%) scaleX(1);
    }

    .navbar-nav .nav-link.active {
      color: var(--active-color);
      font-weight: 700;
    }

    .navbar-nav .nav-link.active::after {
      transform: translateX(-50%) scaleX(1);
      background-color: var(--secondary-color);
    }

    /* Navbar toggler animation */
    .navbar-toggler {
      border: none;
      position: relative;
      width: 30px;
      height: 24px;
      cursor: pointer;
      background: transparent !important;
      transition: transform 0.3s ease;
    }

    .navbar-toggler span {
      display: block;
      position: absolute;
      height: 3px;
      width: 100%;
      background-color: var(--link-color);
      border-radius: 3px;
      opacity: 1;
      left: 0;
      transition: all 0.3s ease;
    }

    .navbar-toggler span:nth-child(1) {
      top: 0;
    }

    .navbar-toggler span:nth-child(2) {
      top: 10px;
    }

    .navbar-toggler span:nth-child(3) {
      top: 20px;
    }

    .navbar-toggler.collapsed span:nth-child(1) {
      transform: rotate(45deg);
      top: 10px;
    }

    .navbar-toggler.collapsed span:nth-child(2) {
      opacity: 0;
    }

    .navbar-toggler.collapsed span:nth-child(3) {
      transform: rotate(-45deg);
      top: 10px;
    }

    @media (max-width: 992px) {
      .navbar-nav .nav-link {
        padding: 1rem 0;
      }
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid px-4">
    <a class="navbar-brand fw-bold" href="dashboard.php">📰 MyBlogApp</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
      <ul class="navbar-nav mb-2 mb-lg-0">
        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <li class="nav-item">
              <a 
                class="nav-link <?= $currentFile === 'admin.php' ? 'active' : ''; ?>" 
                href="admin.php"
              >🛠️ Admin Panel</a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'dashboard.php' ? 'active' : ''; ?>" 
              href="dashboard.php"
            >🏠 Dashboard</a>
          </li>
          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'view.php' ? 'active' : ''; ?>" 
              href="view.php"
            >🌍 All Posts</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="log-out.php">🚪 Log Out</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'log-in.php' ? 'active' : ''; ?>" 
              href="log-in.php"
            >🔐 Login</a>
          </li>
          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'register.php' ? 'active' : ''; ?>" 
              href="register.php"
            >📝 Register</a>
          </li>
          <li class="nav-item">
            <a 
              class="nav-link <?= $currentFile === 'view.php' ? 'active' : ''; ?>" 
              href="view.php"
            >🌍 Browse Posts</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Navbar toggler animation toggle
  const toggler = document.querySelector('.navbar-toggler');
  toggler.addEventListener('click', () => {
    toggler.classList.toggle('collapsed');
  });

  // Optional: Change navbar background on scroll for subtle effect
  const navbar = document.querySelector('nav.navbar');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar.style.backgroundColor = 'rgba(31, 41, 55, 1)';
      navbar.style.boxShadow = '0 2px 15px rgba(79, 70, 229, 0.6)';
    } else {
      navbar.style.backgroundColor = 'rgba(31, 41, 55, 0.9)';
      navbar.style.boxShadow = '0 2px 10px rgba(79, 70, 229, 0.4)';
    }
  });
</script>
</body>
</html>




