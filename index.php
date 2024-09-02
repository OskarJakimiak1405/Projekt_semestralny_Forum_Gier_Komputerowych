<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->query("SELECT topics.id, topics.title, topics.created_at, users.username, users.avatar, GROUP_CONCAT(tags.name SEPARATOR ', ') AS tags
FROM topics 
JOIN users ON topics.user_id = users.id 
LEFT JOIN topic_tags ON topics.id = topic_tags.topic_id
LEFT JOIN tags ON topic_tags.tag_id = tags.id
GROUP BY topics.id
ORDER BY topics.created_at DESC");
$topics = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Najlepsze Forum Gier Komputerowych</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="index.php">Strona Główna</a>
        <a href="search.php">Wyszukiwarka</a>
        <a href="rules.php">Regulamin</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="create_topic.php">Wstaw wpis</a>
            <?php if (isAdmin()): ?>
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
    <h1>Najświeższe tematy</h1>
    <ul>
        <?php foreach ($topics as $topic): ?>
            <li>
                <a class="add" href="topic.php?id=<?= $topic['id'] ?>"><?= htmlspecialchars($topic['title']) ?></a>
                <?php if (!empty($topic['tags'])): ?>
                    <div class="tags">Tagi: <?= htmlspecialchars($topic['tags']) ?></div>
                <?php endif; ?>
                <div class="topic-info">
                    <?php if (!empty($topic['avatar'])): ?>
                        <img class="avatar" src="uploads/<?= htmlspecialchars($topic['avatar']) ?>" alt="Avatar" style="width:20px;height:20px;">
                    <?php else: ?>
                        <img src="default-avatar.png" alt="No Avatar" style="width:20px;height:20px;">
                    <?php endif; ?>
                    <span><?= htmlspecialchars($topic['username']) ?></span>
                </div>
                <p><?= $topic['created_at'] ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
