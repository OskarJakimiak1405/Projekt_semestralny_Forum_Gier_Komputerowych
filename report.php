<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Użytkownik nie jest zalogowany.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $topic_id = $data['topic_id'];
    $user_id = $_SESSION['user_id'];

    // Sprawdzenie, czy użytkownik już zgłosił ten wpis
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reports WHERE topic_id = ? AND user_id = ?");
    $stmt->execute([$topic_id, $user_id]);

    if ($stmt->fetchColumn() > 0) {
        exit(json_encode(['error' => 'Wpis został już zgłoszony przez Ciebie.']));
    }

    // Dodanie zgłoszenia
    $stmt = $conn->prepare("INSERT INTO reports (user_id, topic_id, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $topic_id]);

    echo json_encode(['success' => true]);
}
?>
