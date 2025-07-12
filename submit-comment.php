<?php
session_start();
include 'config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $news_id = (int)$_POST['news_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);

    if ($news_id && $name && $email && $comment) {
        $stmt = $conn->prepare("INSERT INTO comments (news_id, name, email, comment, approved, posted_on) VALUES (?, ?, ?, ?, 0, NOW())");
        $stmt->bind_param("isss", $news_id, $name, $email, $comment);
        if ($stmt->execute()) {
            // Redirect back to article with a success message (you can improve with session flash messages)
            header("Location: news-single.php?slug=" . urlencode($_GET['slug']) . "&msg=comment_submitted");
            exit();
        } else {
            echo "Error submitting comment.";
        }
    } else {
        echo "Please fill all required fields.";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
