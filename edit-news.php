<?php
include 'config.php';
$id = intval($_GET['id']);
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target = "uploads/news/" . $image;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $conn->query("UPDATE news SET title='$title', content='$content', image='$target' WHERE id=$id");
        } else {
            $conn->query("UPDATE news SET title='$title', content='$content' WHERE id=$id");
        }
    } else {
        $conn->query("UPDATE news SET title='$title', content='$content' WHERE id=$id");
    }
    header("Location: admin.php");
    exit();
}

$result = $conn->query("SELECT * FROM news WHERE id=$id");
$news = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit News Article</title>
<style>
  body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea, #764ba2);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .form-container {
    background: white;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    max-width: 600px;
    width: 100%;
    position: relative;
  }
  h3 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
  }
  label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #444;
  }
  input[type="text"],
  textarea,
  input[type="file"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 20px;
    border: 1.5px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }
  input[type="text"]:focus,
  textarea:focus,
  input[type="file"]:focus {
    border-color: #764ba2;
    outline: none;
  }
  textarea {
    resize: vertical;
    min-height: 120px;
  }
  img.current-image {
    display: block;
    margin: 0 auto 20px auto;
    max-width: 250px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  input[type="submit"] {
    background: #764ba2;
    color: white;
    border: none;
    padding: 12px 25px;
    font-size: 1.1rem;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
  }
  input[type="submit"]:hover {
    background-color: #5a3780;
  }

  /* Version badge */
  .version-badge {
    position: fixed;
    bottom: 15px;
    right: 15px;
    background: rgba(118, 75, 162, 0.85);
    color: white;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    z-index: 1000;
    user-select: none;
    pointer-events: none;
  }

  /* Responsive adjustments */
  @media (max-width: 480px) {
    .form-container {
      padding: 20px 25px;
    }
    .version-badge {
      font-size: 0.75rem;
      padding: 5px 10px;
      bottom: 10px;
      right: 10px;
    }
  }
</style>
</head>
<body>

<div class="form-container">
  <h3>Edit News Article</h3>

  <?php if (!empty($news['image'])): ?>
    <label>Current Image:</label>
    <img src="<?= htmlspecialchars($news['image']) ?>" alt="Current News Image" class="current-image" />
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($news['title']) ?>" required>

    <label for="content">Content:</label>
    <textarea id="content" name="content" required><?= htmlspecialchars($news['content']) ?></textarea>

    <label for="image">Upload New Image (optional):</label>
    <input type="file" id="image" name="image" accept="image/*">

    <input type="submit" value="Update">
  </form>
</div>

<div class="version-badge">By Leonard Mhone : Version 1.0.0</div>

</body>
</html>
<?php
$conn->close();
?>
