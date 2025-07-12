<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Pagination setup
$perPage = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$totalBods = $conn->query("SELECT COUNT(*) as total FROM bods")->fetch_assoc()['total'] ?? 0;
$totalNews = $conn->query("SELECT COUNT(*) as total FROM news")->fetch_assoc()['total'] ?? 0;
$totalComments = $conn->query("SELECT COUNT(*) as total FROM comments")->fetch_assoc()['total'] ?? 0;

$totalBodPages = ceil($totalBods / $perPage);
$totalNewsPages = ceil($totalNews / $perPage);
$totalCommentPages = ceil($totalComments / $perPage);

// Slug generator
function slugify($text) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    return substr($slug, 0, 191);
}

function getUniqueSlug($conn, $baseSlug) {
    $slug = $baseSlug;
    $i = 1;
    $stmt = $conn->prepare("SELECT id FROM news WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $stmt->store_result();
    while ($stmt->num_rows > 0) {
        $slug = $baseSlug . '-' . $i;
        $i++;
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $stmt->store_result();
    }
    $stmt->close();
    return $slug;
}

// Add Board Member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bod'])) {
    $name = $_POST['name'] ?? '';
    $position = $_POST['position'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $image = $_FILES['image']['name'] ?? '';

    if ($image) {
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    } else {
        $target = '';
    }

    $stmt = $conn->prepare("INSERT INTO bods (name, position, bio, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $position, $bio, $target);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php#bods');
    exit();
}

// ✅ Add News
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news'])) {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $baseSlug = slugify($title);
    $slug = getUniqueSlug($conn, $baseSlug);

    $image = $_FILES['news_image']['name'] ?? '';
    if (!empty($image)) {
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($image));
        $uploadDir = __DIR__ . '/uploads/news/';
        $uploadWebPath = 'uploads/news/' . $filename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        move_uploaded_file($_FILES['news_image']['tmp_name'], $uploadDir . $filename);
    } else {
        $uploadWebPath = '';
    }

    $stmt = $conn->prepare("INSERT INTO news (title, slug, content, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $slug, $content, $uploadWebPath);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php#news');
    exit();
}

// Paginated queries
$bods = $conn->query("SELECT * FROM bods ORDER BY id DESC LIMIT $perPage OFFSET $offset");
$news = $conn->query("SELECT * FROM news ORDER BY posted_on DESC LIMIT $perPage OFFSET $offset");
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$comments = $conn->query("SELECT * FROM comments ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin:0; padding:0; background:#f4f6f9; }
    .wrapper { display:flex; min-height:100vh; }
    .sidebar { background:#2e7d32; width:220px; min-height:100vh; color:#fff; padding:20px; box-sizing:border-box; }
    .sidebar h2 { margin-top:0; }
    .sidebar a { color:#c8e6c9; text-decoration:none; display:block; margin:10px 0; font-weight:bold; }
    .content { flex:1; padding:30px; box-sizing:border-box; }
    .card { background:#fff; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); padding:20px; margin-bottom:30px; }
    h3 { margin-top:0; }
    input, textarea { width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:6px; }
    input[type="submit"] { background:#2e7d32; color:#fff; border:none; cursor:pointer; padding:10px 20px; }
    ul { padding-left:20px; }
    ul li { margin-bottom:15px; }
    .btn-delete, .btn-edit { margin-left:10px; text-decoration:none; font-weight:bold; }
    .btn-delete { color:red; }
    .btn-edit { color:blue; }
    .pagination { margin-top:15px; }
    .pagination a { margin-right:8px; color:#2e7d32; text-decoration:none; font-weight:bold; }
    .footer { background:#2e7d32; text-align:center; padding:15px; font-size:14px; color:#fff; border-top:1px solid #ccc; }
    .slug-link { font-size:12px; color:#555; }
  </style>
</head>
<body>

<div class="wrapper">
  <div class="sidebar">
    <h2>YFSS Admin</h2>
    <a href="#bods">Board Members</a>
    <a href="#news">News Articles</a>
    <a href="#users">Users</a>
    <a href="#comments">Comments</a>
    <a href="change-password.php">Change Password</a>
    <a href="logout.php">Logout</a>
  </div>

  <div class="content">

    <!-- BOD Section -->
    <div class="card" id="bods">
      <h3>Add Board Member</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="add_bod" value="1">
        <label>Name</label>
        <input type="text" name="name" required>
        <label>Position</label>
        <input type="text" name="position" required>
        <label>Bio</label>
        <textarea name="bio" rows="4" required></textarea>
        <label>Image</label>
        <input type="file" name="image" required>
        <input type="submit" value="Add Member">
      </form>

      <h3>Existing Board Members</h3>
      <ul>
        <?php while ($row = $bods->fetch_assoc()): ?>
          <li><strong><?= htmlspecialchars($row['name']) ?></strong> - <?= htmlspecialchars($row['position']) ?>
            <a href="edit-bod.php?id=<?= $row['id'] ?>" class="btn-edit">✏️</a>
            <a href="delete-bod.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this member?')">❌</a>
          </li>
        <?php endwhile; ?>
      </ul>
      <div class="pagination">
        <strong>BOD Pages:</strong>
        <?php for ($i = 1; $i <= $totalBodPages; $i++): ?>
          <a href="?page=<?= $i ?>#bods"><?= $i ?></a>
        <?php endfor; ?>
      </div>
    </div>

    <!-- ✅ News Section -->
    <div class="card" id="news">
      <h3>Post News</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="add_news" value="1">
        <label>Title</label>
        <input type="text" name="title" required>
        <label>Content</label>
        <textarea name="content" rows="6" required></textarea>
        <label>Image</label>
        <input type="file" name="news_image">
        <input type="submit" value="Post News">
      </form>

      <h3>News Articles</h3>
      <ul>
        <?php while ($row = $news->fetch_assoc()): ?>
          <li>
            <strong><?= htmlspecialchars($row['title']) ?></strong> (<?= htmlspecialchars($row['posted_on']) ?>)<br>
            <span class="slug-link">/news/<?= htmlspecialchars($row['slug']) ?></span><br>
            <?php if (!empty($row['image'])): ?>
              <img src="<?= htmlspecialchars($row['image']) ?>" style="max-width:150px; margin:5px 0;"><br>
            <?php endif; ?>
            <a href="edit-news.php?id=<?= $row['id'] ?>" class="btn-edit">✏️</a>
            <a href="delete-news.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this article?')">❌</a>
          </li>
        <?php endwhile; ?>
      </ul>
      <div class="pagination">
        <strong>News Pages:</strong>
        <?php for ($i = 1; $i <= $totalNewsPages; $i++): ?>
          <a href="?page=<?= $i ?>#news"><?= $i ?></a>
        <?php endfor; ?>
      </div>
    </div>

    <!-- Users and Comments Sections (unchanged) -->
    <!-- ... -->
    
  </div>
</div>

<footer class="footer">
  <p><strong>Yissah Foundation Admin Panel</strong> | Version 1.0.0</p>
  <p>&copy; <?= date('Y') ?> Designed by Leonard Mhone (ITEC ICT SOLUTIONS).</p>
</footer>

</body>
</html>
