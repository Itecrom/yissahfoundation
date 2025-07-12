<?php
session_start();
include 'config.php';

// Check login and admin rights
if (!isset($_SESSION['logged_in']) /*|| $_SESSION['role'] !== 'admin'*/) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    die('No comment specified.');
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$comment_id = (int)$_GET['id'];

// Delete comment
$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->close();

$conn->close();

// Redirect back to admin with success message (optional)
header('Location: admin.php#comments');
exit();
?>
