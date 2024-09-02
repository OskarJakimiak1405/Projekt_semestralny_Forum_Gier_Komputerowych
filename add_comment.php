<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic_id = $_POST['topic_id'];
    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO comments (topic_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$topic_id, $user_id, $content]);

    header("Location: topic.php?id=$topic_id");
    exit();
}
?>
