<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || !isAdmin()) {
    http_response_code(403);
    exit(json_encode(['error' => 'Brak dostępu.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $report_id = $data['report_id'];

    // Usuwanie zgłoszenia
    $stmt = $conn->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->execute([$report_id]);

    echo json_encode(['success' => true]);
}
?>
