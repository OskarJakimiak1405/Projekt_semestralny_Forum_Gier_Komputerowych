<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_POST['delete_post'])) {
    $post_id = $_POST['post_id'];
    $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
    $stmt->execute([$post_id]);
}

if (isset($_POST['ban_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("UPDATE users SET banned = TRUE WHERE id = ?");
    $stmt->execute([$user_id]);
}

header("Location: index.php");
?>
