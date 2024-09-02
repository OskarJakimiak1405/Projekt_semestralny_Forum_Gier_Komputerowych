<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Użytkownik nie jest zalogowany.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $isTopic = $data['is_topic'];
    $id = $data['id'];
    $type = $data['type'];
    $user_id = $_SESSION['user_id'];

    if ($isTopic) {
        $stmt = $conn->prepare("DELETE FROM reactions WHERE topic_id = ? AND user_id = ?");
    } else {
        $stmt = $conn->prepare("DELETE FROM reactions WHERE comment_id = ? AND user_id = ?");
    }
    $stmt->execute([$id, $user_id]);

    if ($isTopic) {
        $stmt = $conn->prepare("INSERT INTO reactions (user_id, topic_id, type) VALUES (?, ?, ?)");
    } else {
        $stmt = $conn->prepare("INSERT INTO reactions (user_id, comment_id, type) VALUES (?, ?, ?)");
    }
    $stmt->execute([$user_id, $id, $type]);

    $result = [];
    foreach (['like', 'love', 'haha', 'wow', 'sad', 'angry'] as $reaction) {
        if ($isTopic) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM reactions WHERE topic_id = ? AND type = ?");
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM reactions WHERE comment_id = ? AND type = ?");
        }
        $stmt->execute([$id, $reaction]);
        $result[$reaction] = $stmt->fetchColumn();
    }

    echo json_encode(array_merge($result, ['message' => 'Reakcja została dodana']));
}
?>
