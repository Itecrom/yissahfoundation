<?php
include 'config.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $email && $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, approved, created_at) VALUES (?, ?, ?, 0, NOW())");
        if ($stmt) {
            $stmt->bind_param("sss", $username, $email, $hashed);
            if ($stmt->execute()) {
                $message = "Registration successful. Awaiting admin approval.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Database error.";
        }
    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Register | Yissah Foundation SS</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .form-box {
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      width: 100%;
      max-width: 450px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .form-box h2 {
      margin-bottom: 20px;
      font-weight: 600;
      color: #2e7d32;
    }
    .form-box .form-control {
      border-radius: 6px;
      margin-bottom: 15px;
    }
    .form-box .btn-primary {
      background-color: #2e7d32;
      border: none;
      width: 100%;
    }
    .form-box .btn-primary:hover {
      background-color: #256428;
    }
    .text-center a {
      color: #2e7d32;
    }
  </style>
</head>
<body>

<div class="form-box">
  <h2>Register</h2>
  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <form method="POST">
    <input type="text" name="username" class="form-control" placeholder="Full Name" required>
    <input type="email" name="email" class="form-control" placeholder="Email" required>
    <input type="password" name="password" class="form-control" placeholder="Password" required>
    <button type="submit" class="btn btn-primary">Register</button>
  </form>
  <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
