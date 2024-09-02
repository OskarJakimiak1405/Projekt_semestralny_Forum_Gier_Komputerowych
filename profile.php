<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
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
    <h1>Profil</h1>
    <div class="content">
        <p>Nazwa użytkownika: <?= htmlspecialchars($user['username']) ?></p>
        <p>Email: <?= htmlspecialchars($user['email']) ?></p>
        
        <?php if (!empty($user['avatar'])): ?>
            <img class="avatar" src="uploads/<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" style="width:auto;height:150px;">
        <?php else: ?>
            <p>Nie przesłano avataru.</p>
        <?php endif; ?>

        <form action="upload_avatar.php" method="post" enctype="multipart/form-data">
            <label for="avatar">Prześlij avatar</label>
            <input type="file" name="avatar" id="avatar">
            <input type="submit" value="Upload">
        </form>
    </div>
</body>
</html>
