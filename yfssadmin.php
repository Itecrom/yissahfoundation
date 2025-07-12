<?php
session_start();
include 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password_hash FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $password_hash);
        $stmt->fetch();
        if (password_verify($password, $password_hash)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_username'] = $username;
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Login</title>
<style>
  /* Full-page background with overlay */
  body, html {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('images/bg_2') no-repeat center center fixed;
    background-size: cover;
    position: relative;
  }
  /* Dark overlay */
  body::before {
    content: "";
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 0;
  }

  /* Center the login box */
  .box {
    position: relative;
    z-index: 1;
    background: rgba(255, 255, 255, 0.95);
    padding: 30px 25px;
    border-radius: 12px;
    box-shadow: 0 12px 25px rgba(0,0,0,0.4);
    max-width: 360px;
    width: 90%;
    margin: auto;
    top: 50%;
    transform: translateY(-50%);
    text-align: center;
  }

  h2 {
    margin-bottom: 25px;
    color: #333;
  }

  input[type="text"],
  input[type="password"] {
    width: 100%;
    padding: 12px 14px;
    margin: 12px 0;
    border: 1.5px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }

  input[type="text"]:focus,
  input[type="password"]:focus {
    border-color: #4caf50;
    outline: none;
  }

  input[type="submit"] {
    background: #4caf50;
    color: white;
    border: none;
    padding: 14px 0;
    font-size: 1.1rem;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    margin-top: 15px;
    transition: background-color 0.3s ease;
  }

  input[type="submit"]:hover {
    background-color: #388e3c;
  }

  p.error {
    color: #d32f2f;
    font-weight: 600;
    margin: 15px 0 0 0;
  }

  .register-link {
    margin-top: 20px;
    font-size: 0.9rem;
  }
  .register-link a {
    color: #4caf50;
    text-decoration: none;
    font-weight: 600;
  }
  .register-link a:hover {
    text-decoration: underline;
  }

  /* Copyright at bottom */
  .copyright {
    position: fixed;
    bottom: 8px;
    width: 100%;
    text-align: center;
    color: #ddd;
    font-size: 0.85rem;
    user-select: none;
    z-index: 1;
    font-weight: 300;
    text-shadow: 0 0 4px rgba(0,0,0,0.6);
  }

  /* Responsive tweaks */
  @media (max-width: 400px) {
    .box {
      padding: 20px 15px;
      max-width: 320px;
    }
    input[type="submit"] {
      font-size: 1rem;
      padding: 12px 0;
    }
  }
</style>
</head>
<body>

  <div class="box">
    <h2>Admin Login</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required autocomplete="off" />
      <input type="password" name="password" placeholder="Password" required />
      <input type="submit" value="Login" />
    </form>
    <p class="register-link">Don't have an account? <a href="registeradmin.php">Register here</a></p>
  </div>

  <div class="copyright">
    &copy; <?= date('Y') ?> YFSS. All rights reserved | By Leonard JJ Mhone (ITEC ICT SOLUTIONS)
  </div>

</body>
</html>
