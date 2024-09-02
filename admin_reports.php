<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die('Dostęp zabroniony.');
}

// Pobieranie wszystkich zgłoszeń
$stmt = $conn->prepare("SELECT reports.id, topics.title, users.username, reports.created_at 
                        FROM reports 
                        JOIN topics ON reports.topic_id = topics.id 
                        JOIN users ON reports.user_id = users.id 
                        ORDER BY reports.created_at DESC");
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$is_admin = isAdmin();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zgłoszenia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
        <a href="index.php">Strona Główna</a>
        <a href="search.php">Wyszukiwanie</a>
        <a href="rules.php">Regulamin</a>
        <?php if (isset($_SESSION['username'])): ?>
        <a href="create_topic.php">Wstaw wpis</a>
            <?php if ($is_admin): ?>
                <a href="admin_panel.php">Panel Administratora</a>
            <?php endif; ?>
            <a href="admin_reports.php">Powiadomienia</a>
        <a href="profile.php">Profil</a>
        <a href="logout.php">Wyloguj</a>
        <?php else: ?>
            <a href="register.php">Rejestracja</a>
            <a href="login.php">Logowanie</a>
        <?php endif; ?>
    </nav>
    <img src="logo.png" class="logo" alt="logo">
    <h1>Zgłoszenia</h1>
    <ul class="report-list">
        <?php foreach ($reports as $report): ?>
            <li class="report-item">
                <span><strong>ID:</strong> <?= htmlspecialchars($report['id']) ?></span>
                <span><strong>Tytuł Tematu:</strong> <?= htmlspecialchars($report['title']) ?></span>
                <span><strong>Zgłoszony przez:</strong> <?= htmlspecialchars($report['username']) ?></span>
                <span><strong>Data zgłoszenia:</strong> <?= htmlspecialchars($report['created_at']) ?></span>
                <?php if ($is_admin): ?>
                    <button onclick="deleteReport(<?= $report['id'] ?>)">Usuń zgłoszenie</button>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
<?php if ($is_admin): ?>
<script>
function deleteReport(reportId) {
    if (confirm('Czy na pewno chcesz usunąć to zgłoszenie?')) {
        fetch('delete_report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                report_id: reportId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Zgłoszenie zostało usunięte.');
                location.reload();
            } else if (data.error) {
                alert('Błąd: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>
<?php endif; ?>
</html>
