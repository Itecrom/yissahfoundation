<?php
include 'config.php';
$id = intval($_GET['id']);
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$conn->query("DELETE FROM bods WHERE id=$id");
header("Location: admin.php");
exit();
?>
