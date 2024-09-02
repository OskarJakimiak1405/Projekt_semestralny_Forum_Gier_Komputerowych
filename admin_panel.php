<?php
require 'db.php';

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_post'])) {
        $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
        $stmt->execute([$_POST['post_id']]);
    } elseif (isset($_POST['delete_comment'])) {
        $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$_POST['comment_id']]);
    } elseif (isset($_POST['ban_user'])) {
        $stmt = $conn->prepare("UPDATE users SET banned = 1 WHERE id = ?");
        $stmt->execute([$_POST['user_id']]);
    } elseif (isset($_POST['unban_user'])) {
        $stmt = $conn->prepare("UPDATE users SET banned = 0 WHERE id = ?");
        $stmt->execute([$_POST['user_id']]);
    }
}

$posts = $conn->query("SELECT * FROM topics")->fetchAll();
$comments = $conn->query("SELECT * FROM comments")->fetchAll();
$users = $conn->query("SELECT * FROM users")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Panel Administratora</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="index.php">Strona Główna</a>
        <a href="search.php">Wyszukiwanie</a>
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
    <h1>Panel Administratora</h1>

    <h2>Tematy</h2>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <?= htmlspecialchars($post['title']) ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <button type="submit" name="delete_post">Usuń</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Komentarze</h2>
    <ul>
        <?php foreach ($comments as $comment): ?>
            <li>
                <?= htmlspecialchars($comment['content']) ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                    <button type="submit" name="delete_comment">Usuń</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Użytkownicy</h2>
    <ul>
        <?php foreach ($users as $user): ?>
            <li>
                <?= htmlspecialchars($user['username']) ?> - <?= $user['banned'] ? 'Zablokowany' : 'Aktywny' ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <?php if ($user['banned']): ?>
                        <button type="submit" name="unban_user">Odblokuj</button>
                    <?php else: ?>
                        <button type="submit" name="ban_user">Zablokuj</button>
                    <?php endif; ?>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
