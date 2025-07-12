<?php
session_start();
include 'config.php';

// Check login and admin rights
if (!isset($_SESSION['logged_in']) /*|| $_SESSION['role'] !== 'admin'*/) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    die('Missing parameters.');
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$comment_id = (int)$_GET['id'];
$status = ($_GET['status'] == '1') ? 1 : 0;

$stmt = $conn->prepare("UPDATE comments SET approved = ? WHERE id = ?");
$stmt->bind_param("ii", $status, $comment_id);
$stmt->execute();
$stmt->close();

$conn->close();

header('Location: admin.php#comments');
exit();
?>
