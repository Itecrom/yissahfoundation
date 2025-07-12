<?php
include 'config.php';
$id = intval($_GET['id']);
$status = intval($_GET['status']);

$conn->query("UPDATE users SET approved = $status WHERE id = $id");
header("Location: admin.php#users");
