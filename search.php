<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit();
    } else {
        echo "Nieprawidłowy login lub hasło.";
    }
}

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = $_GET['query'];

    $stmt = $conn->prepare("SELECT DISTINCT topics.id, topics.title, users.username 
    FROM topics 
    JOIN users ON topics.user_id = users.id 
    WHERE topics.title LIKE ?");
    $stmt->execute(['%' . $query . '%']);
    $results = $stmt->fetchAll();

    $stmt_tag = $conn->prepare("SELECT DISTINCT topics.id, topics.title, users.username 
    FROM topics 
    JOIN users ON topics.user_id = users.id 
    JOIN topic_tags ON topics.id = topic_tags.topic_id 
    JOIN tags ON topic_tags.tag_id = tags.id 
    WHERE tags.name LIKE ?");
    $stmt_tag->execute(['%' . $query . '%']);
    $results_tag = $stmt_tag->fetchAll();

    $results = array_merge($results, $results_tag);
    $results = array_unique($results, SORT_REGULAR);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wyszukiwanie</title>
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
    <h1>Wyszukiwarka</h1>
    <form method="GET">
        <input type="text" name="query" placeholder="Szukaj tematu lub tagu...">
        <button type="submit">Szukaj</button>
    </form>

    <h2>Wyniki wyszukiwania:</h2>
    <ul>
        <?php foreach ($results as $result): ?>
            <li>
                <a href="topic.php?id=<?= $result['id'] ?>"><?= htmlspecialchars($result['title']) ?></a> by <?= htmlspecialchars($result['username']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
