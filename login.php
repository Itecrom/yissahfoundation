<?php
include 'config.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, password, approved FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashedPassword, $approved);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            if ($approved == 1) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;
                header("Location: home.php");
                exit;
            } else {
                $error = "Your account is awaiting admin approval.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with that email.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login | Yissah Foundation SS</title>
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
      max-width: 400px;
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
  <h2>Login</h2>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <input type="email" name="email" class="form-control" placeholder="Email" required>
    <input type="password" name="password" class="form-control" placeholder="Password" required>
    <button type="submit" class="btn btn-primary">Login</button>
  </form>
  <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
</div>

</body>
</html>
