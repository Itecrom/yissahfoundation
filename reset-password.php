<?php
session_start();
include 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$showForm = false;

if (!isset($_GET['token'])) {
    die("No token provided.");
}

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Invalid token.");
}

$user = $result->fetch_assoc();
$expires = strtotime($user['reset_expires']);
$now = time();

if ($now > $expires) {
    die("Token expired. Please request a new password reset.");
}

$showForm = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($password !== $password_confirm) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $update->bind_param("si", $hashed, $user['id']);
        if ($update->execute()) {
            $message = "Password successfully reset. You can now <a href='login.php'>login</a>.";
            $showForm = false;
        } else {
            $message = "Failed to reset password.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password - Yissah Foundation</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- Your main CSS -->
    <style>
      body {
        background: #f8f9fa;
        font-family: 'Poppins', sans-serif;
      }
      .reset-container {
        max-width: 400px;
        margin: 80px auto;
        padding: 30px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
      }
      .btn-primary {
        background: #007bff;
        border-color: #007bff;
      }
      .btn-primary:hover {
        background: #0056b3;
        border-color: #004085;
      }
      a {
        color: #007bff;
      }
      a:hover {
        text-decoration: none;
        color: #0056b3;
      }
      h2 {
        font-weight: 700;
        margin-bottom: 20px;
        color: #333;
      }
    </style>
</head>
<body>
  <div class="reset-container">
    <h2>Reset Password</h2>

    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($showForm): ?>
    <form method="POST" action="" novalidate>
        <div class="form-group">
            <label class="font-weight-bold">New Password</label>
            <input type="password" name="password" class="form-control form-control-lg" required minlength="6" placeholder="Enter new password">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Confirm New Password</label>
            <input type="password" name="password_confirm" class="form-control form-control-lg" required minlength="6" placeholder="Confirm new password">
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg">Reset Password</button>
    </form>
    <?php endif; ?>

    <p class="mt-3 text-center"><a href="news-single.php">Back to News</a></p>
  </div>

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
