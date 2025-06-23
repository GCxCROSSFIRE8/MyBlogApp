<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

$perPage = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$search = trim($_GET['search'] ?? '');

try {
    // COUNT query
    if ($search !== '') {
        $sqlCount = "SELECT COUNT(*) FROM posts
                     JOIN users ON posts.user_id = users.id
                     WHERE posts.deleted_at IS NULL
                     AND (posts.title LIKE ? OR posts.content LIKE ? OR users.email LIKE ?)";
        $stmt = $conn->prepare($sqlCount);
        $searchTerm = "%$search%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM posts
                                JOIN users ON posts.user_id = users.id
                                WHERE posts.deleted_at IS NULL");
        $stmt->execute();
    }

    $total = (int)$stmt->fetchColumn();
    $totalPages = max(1, (int)ceil($total / $perPage));

    // DATA query
    if ($search !== '') {
        $sql = "SELECT posts.*, users.name AS author_name, users.email AS author_email
                FROM posts
                JOIN users ON posts.user_id = users.id
                WHERE posts.deleted_at IS NULL
                AND (posts.title LIKE ? OR posts.content LIKE ? OR users.email LIKE ?)
                ORDER BY posts.created_at DESC
                LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $offset, $perPage]);
    } else {
        $sql = "SELECT posts.*, users.name AS author_name, users.email AS author_email
                FROM posts
                JOIN users ON posts.user_id = users.id
                WHERE posts.deleted_at IS NULL
                ORDER BY posts.created_at DESC
                LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$offset, $perPage]);
    }

    $posts = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>⚠️ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>All Posts - MyBlogApp</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  /* Color palette */
  :root {
    --primary-color: #4f46e5; /* Indigo-600 */
    --secondary-color: #f59e0b; /* Amber-500 */
    --bg-light: #f9fafb;
    --text-primary: #111827; /* Gray-900 */
    --text-secondary: #6b7280; /* Gray-500 */
  }

  body {
    background-color: var(--bg-light);
    color: var(--text-primary);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  h2 {
    font-weight: 700;
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    position: relative;
    overflow: hidden;
  }

  /* Text animation: gradient slide */
  h2::after {
    content: '🌍 All Posts';
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

  /* Search input style */
  form input[type="text"] {
    max-width: 480px;
    margin: 0 auto 2rem auto;
    display: block;
    border: 2px solid var(--primary-color);
    border-radius: 0.375rem;
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }
  form input[type="text"]:focus {
    outline: none;
    border-color: var(--secondary-color);
    box-shadow: 0 0 10px var(--secondary-color);
  }

  /* Post cards */
  .card {
    border-radius: 1rem;
    box-shadow:
      0 4px 6px rgba(0, 0, 0, 0.1),
      0 1px 3px rgba(0, 0, 0, 0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    background: #fff;
  }
  .card:hover {
    transform: translateY(-8px);
    box-shadow:
      0 12px 20px rgba(79, 70, 229, 0.4),
      0 8px 10px rgba(245, 158, 11, 0.3);
  }

  /* Card body text */
  .card-body h4 {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    letter-spacing: 0.03em;
    transition: color 0.3s ease;
  }
  .card:hover .card-body h4 {
    color: var(--secondary-color);
    text-decoration: underline;
  }

  .card-body small {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-style: italic;
    margin-bottom: 1rem;
    display: block;
  }

  .card-body p {
    font-size: 1.05rem;
    line-height: 1.5;
    color: var(--text-primary);
  }

  /* Pagination */
  .pagination {
    margin-top: 3rem;
  }
  .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 12px var(--primary-color);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }
  .page-link {
    color: var(--primary-color);
    font-weight: 500;
    border-radius: 0.5rem;
    transition: background-color 0.3s ease, color 0.3s ease;
  }
  .page-link:hover {
    background-color: var(--secondary-color);
    color: white;
    box-shadow: 0 0 10px var(--secondary-color);
  }

  /* Responsive tweaks */
  @media (max-width: 576px) {
    h2 {
      font-size: 2rem;
    }
    form input[type="text"] {
      width: 90%;
    }
  }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
  <h2>🌍 All Posts</h2>

  <form method="GET" role="search" aria-label="Search posts">
    <input 
      type="text" 
      name="search" 
      placeholder="Search by title, content or author email..." 
      value="<?= htmlspecialchars($search) ?>" 
      autocomplete="off"
      aria-describedby="searchHelp"
      aria-label="Search posts"
    >
  </form>

  <?php if ($posts): foreach ($posts as $p): ?>
    <div class="card mb-4" role="article" tabindex="0" aria-label="Post titled <?= htmlspecialchars($p['title']) ?>">
      <div class="card-body">
        <h4><?= htmlspecialchars($p['title']) ?></h4>
        <small>by <?= htmlspecialchars($p['author_email']) ?> on <?= htmlspecialchars($p['created_at']) ?></small>
        <p><?= nl2br(htmlspecialchars($p['content'])) ?></p>
      </div>
    </div>
  <?php endforeach; else: ?>
    <div class="alert alert-info text-center">No posts found.</div>
  <?php endif; ?>

  <?php if ($totalPages > 1): ?>
    <nav aria-label="Pagination navigation">
      <ul class="pagination justify-content-center" role="list">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i === $page ? 'active' : '' ?>" role="listitem">
            <a 
              class="page-link" 
              href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
              aria-current="<?= $i === $page ? 'page' : false ?>"
            >
              <?= $i ?>
            </a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>






