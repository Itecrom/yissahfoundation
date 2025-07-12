<?php
session_start();
include 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND approved = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $userId = $user['id'];
            
            $token = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', time() + 3600);

            $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $update->bind_param("ssi", $token, $expires, $userId);
            $update->execute();

            $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=" . $token;

            $subject = "Password Reset Request";
            $headers = "From: no-reply@yissahfoundation.org\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $body = "
                <p>Hello,</p>
                <p>You requested a password reset for your account. Click the link below to reset your password:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request this, please ignore this email.</p>
                <p>Yissah Foundation Secondary School</p>
            ";

            mail($email, $subject, $body, $headers);

            $message = "Password reset link sent to your email.";
        } else {
            $message = "Email not found or account not approved.";
        }
        $stmt->close();
    } else {
        $message = "Please enter a valid email address.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password - Yissah Foundation</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- Your main CSS -->
    <style>
      body {
        background: #f8f9fa;
        font-family: 'Poppins', sans-serif;
      }
      .forgot-container {
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
  <div class="forgot-container">
    <h2>Forgot Password</h2>
    <?php if ($message): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST" action="forgot-password.php" novalidate>
        <div class="form-group">
            <label for="email" class="font-weight-bold">Enter your registered email address</label>
            <input type="email" id="email" name="email" class="form-control form-control-lg" required autofocus placeholder="your.email@example.com">
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg">Send Reset Link</button>
    </form>
    <p class="mt-3 text-center"><a href="news-single.php">Back to News</a></p>
  </div>

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
