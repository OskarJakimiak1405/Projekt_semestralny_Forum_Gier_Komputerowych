<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];
    $tags = isset($_POST['tags']) ? $_POST['tags'] : [];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_path = "uploads/" . basename($image_name);
        move_uploaded_file($image_tmp_name, $image_path);
    } else {
        $image_path = null;
    }

    $stmt = $conn->prepare("INSERT INTO topics (title, user_id, content, image_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $user_id, $content, $image_path]);
    $topic_id = $conn->lastInsertId();

    foreach ($tags as $tag_id) {
        $stmt = $conn->prepare("INSERT INTO topic_tags (topic_id, tag_id) VALUES (?, ?)");
        $stmt->execute([$topic_id, $tag_id]);
    }

    header("Location: index.php");
    exit();
}

$stmt = $conn->query("SELECT id, name FROM tags");
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stwórz temat</title>
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
    <h1>Stwórz temat</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>Tytuł: <br><input type="text" name="title" required></label><br>
        <label>Zawartość: <br><textarea name="content" required></textarea></label><br>
        <label>Obrazek: <br><input type="file" name="image"></label><br>
        <label>Tagi: <br>
            <select name="tags[]" multiple>
                <?php foreach ($tags as $tag): ?>
                    <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">Stwórz</button>
    </form>
</body>
</html>
