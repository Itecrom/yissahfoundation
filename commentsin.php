<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        if ($user['approved'] == 1) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php");
        } else {
            echo "Your account is pending approval.";
        }
    } else {
        echo "Invalid login credentials.";
    }
}
?>

<form method="POST">
  <h2>Login</h2>
  <label>Email</label>
  <input type="email" name="email" required>
  <label>Password</label>
  <input type="password" name="password" required>
  <input type="submit" value="Login">
</form>
