<?php
session_start();
include 'config.php';

// Check if admin logged in
if (!isset($_SESSION['logged_in']) /* || $_SESSION['role'] !== 'admin' */) {
    header('Location: yfssadmin.php');
    exit();
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];  // assuming you store logged in user's ID here

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Fetch current hashed password from DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    if (!$stmt->fetch()) {
        $errors[] = "User not found.";
    }
    $stmt->close();

    // Validate current password
    if (!password_verify($current_password, $hashed_password)) {
        $errors[] = "Current password is incorrect.";
    }

    // Validate new passwords match
    if ($new_password !== $confirm_password) {
        $errors[] = "New password and confirmation do not match.";
    }

    // Validate new password length (optional)
    if (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    }

    // If no errors, update password
    if (empty($errors)) {
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $new_hashed, $user_id);
        if ($update->execute()) {
            $success = "Password updated successfully.";
        } else {
            $errors[] = "Failed to update password.";
        }
        $update->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; }
        input[type=password] { width: 300px; padding: 8px; }
        .error { color: red; }
        .success { color: green; }
        button { padding: 10px 20px; background: #2e7d32; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h2>Change Password</h2>

<?php if ($errors): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" id="current_password" required />
    </div>

    <div class="form-group">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required />
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required />
    </div>

    <button type="submit">Change Password</button>
</form>

<p><a href="admin.php">Back to Admin Dashboard</a></p>

</body>
</html>
