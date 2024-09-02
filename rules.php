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
    <title>Regulamin</title>
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
    <h1>Regulamin</h1>
    <p class="rules">
        1. Każdy użytkownik jest zobowiązany do przestrzegania zasad kultury osobistej.<br>
        2. Zabronione jest umieszczanie treści niezgodnych z prawem oraz treści o tematyce niecenzuralnej.<br>
        3. Administratorzy forum mają wszelkie prawa do usuwania postów i banowania użytkowników, którzy naruszą regulamin forum.<br>
        4. Każdy użytkownik jest zobowiązany do poszanowania prywatności innych użytkowników.<br>
        5. Jeżeli na forum zostanie zareklamowana inna strona bez zgody administracji, wiąże się to z banem oraz usunięciem konta użytkownika.<br>
    </p>
</body>
</html>
