<?php
require 'db.php';

if (!isset($_GET['id'])) {
    die('Brak ID tematu.');
}

$topic_id = $_GET['id'];
$reactions_types = [
    'like' => '👍',
    'love' => '❤️',
    'haha' => '😂',
    'wow' => '😮',
    'sad' => '😢',
    'angry' => '😡'
];

$stmt = $conn->prepare("SELECT topics.id, topics.title, topics.content, topics.image_path, users.username FROM topics JOIN users ON topics.user_id = users.id WHERE topics.id = ?");
$stmt->execute([$topic_id]);
$topic = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$topic) {
    die('Temat nie istnieje.');
}

$topic_reactions = [];
foreach ($reactions_types as $reaction => $emoji) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reactions WHERE topic_id = ? AND type = ?");
    $stmt->execute([$topic_id, $reaction]);
    $topic_reactions[$reaction] = $stmt->fetchColumn();
}

$stmt = $conn->prepare("SELECT comments.id, comments.content, comments.created_at, users.username, users.avatar FROM comments JOIN users ON comments.user_id = users.id WHERE comments.topic_id = ? ORDER BY comments.created_at DESC");
$stmt->execute([$topic_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($topic['title']) ?></title>
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
    <div class="content">
        <h1><?= htmlspecialchars($topic['title']) ?></h1>
        <p>Stworzone przez <?= htmlspecialchars($topic['username']) ?></p>
        <p>Treść: <?= nl2br(htmlspecialchars($topic['content'])) ?></p>
        <?php if ($topic['image_path']): ?>
            <img src="<?= htmlspecialchars($topic['image_path']) ?>" height="430px" width="auto" alt="<?= htmlspecialchars($topic['title']) ?>">
        <?php endif; ?>
        <div>
            <?php foreach ($reactions_types as $reaction => $emoji): ?>
                <button onclick="addReaction(true, <?= $topic['id'] ?>, '<?= $reaction ?>')">
                    <?= $emoji ?>
                </button>
                <span id="topic-<?= $reaction ?>"><?= $topic_reactions[$reaction] ?></span>
            <?php endforeach; ?>
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <button onclick="reportTopic(<?= $topic['id'] ?>)">Zgłoś wpis</button>
        <?php endif; ?>
    </div>

    <h2>Komentarze</h2>
    <ul>
        <?php foreach ($comments as $comment): ?>
            <li>
                <div class="comment-info">
                    <?php if (!empty($comment['avatar'])): ?>
                        <img class="avatar" src="uploads/<?= htmlspecialchars($comment['avatar']) ?>" alt="Avatar" style="width:20px;height:20px;">
                    <?php else: ?>
                        <img src="default-avatar.png" alt="No Avatar" style="width:20px;height:20px;">
                    <?php endif; ?> 
                    <span><?= htmlspecialchars($comment['username']) ?></span>: <?= htmlspecialchars($comment['content']) ?>
                </div>
                <div>
                    <?php
                    foreach ($reactions_types as $reaction => $emoji):
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM reactions WHERE comment_id = ? AND type = ?");
                        $stmt->execute([$comment['id'], $reaction]);
                        $comment_reaction_count = $stmt->fetchColumn();
                    ?>
                        <button onclick="addReaction(false, <?= $comment['id'] ?>, '<?= $reaction ?>')">
                            <?= $emoji ?> 
                        </button>
                        <span id="comment-<?= $reaction ?>-<?= $comment['id'] ?>"><?= $comment_reaction_count ?></span>
                    <?php endforeach; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (isset($_SESSION['username'])): ?>
        <h2>Dodaj komentarz</h2>
        <form method="POST" action="add_comment.php">
            <input type="hidden" name="topic_id" value="<?= $topic['id'] ?>">
            <label>Zawartość: <br><textarea name="content" required></textarea></label><br>
            <button type="submit">Dodaj komentarz</button>
        </form>
    <?php endif; ?>
</body>
<script>
function addReaction(isTopic, id, type) {
    fetch('add_reaction.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            is_topic: isTopic,
            id: id,
            type: type
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
        }
        <?php foreach ($reactions_types as $reaction => $emoji): ?>
            if (data.<?= $reaction ?> !== undefined) {
                if (isTopic) {
                    document.getElementById('topic-<?= $reaction ?>').textContent = data.<?= $reaction ?>;
                } else {
                    document.getElementById('comment-<?= $reaction ?>-' + id).textContent = data.<?= $reaction ?>;
                }
            }
        <?php endforeach; ?>
    })
    .catch(error => console.error('Error:', error));
}

function reportTopic(topicId) {
    if (confirm('Czy na pewno chcesz zgłosić ten wpis?')) {
        fetch('report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                topic_id: topicId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Wpis został zgłoszony.');
            } else if (data.error) {
                alert('Błąd: ' + data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>
</html>
