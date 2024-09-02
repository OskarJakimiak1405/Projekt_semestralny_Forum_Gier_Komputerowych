<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$action = $_GET['action'];
$id = $_GET['id'];

if ($action && $id) {
    switch ($action) {
        case 'delete_topic':
            $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
            $stmt->execute([$id]);
            break;
        case 'delete_comment':
            $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->execute([$id]);
            break;
        case 'toggle_ban':
            $stmt = $conn->prepare("UPDATE users SET banned = NOT banned WHERE id = ?");
            $stmt->execute([$id]);
            break;
    }
}

header("Location: admin_panel.php");
exit();
?>
