<?php
session_start();
include('conLeaderboard.php'); // Connexion PDO

// Vérifie que l'user est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupérer le topic_id
$topic_id = $_GET['topic_id'] ?? null;
if (!$topic_id) {
    echo "Topic introuvable.";
    exit;
}

// 1) Vérifier que le topic existe
$sql = "SELECT t.title, t.created_at, u.username
        FROM forum_topics t
        JOIN users u ON t.user_id = u.user_id
        WHERE t.topic_id = :topic_id
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':topic_id' => $topic_id]);
$topic = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$topic) {
    echo "Topic introuvable ou supprimé.";
    exit;
}

// 2) Gérer l'envoi d'un nouveau message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO forum_posts (topic_id, user_id, message) VALUES (:topic_id, :user_id, :message)");
        $stmt->execute([
            ':topic_id' => $topic_id,
            ':user_id'  => $_SESSION['user_id'],
            ':message'  => $message
        ]);
        // Pour éviter le repost en rafraîchissant la page, on peut rediriger
        header("Location: view_topic.php?topic_id=" . urlencode($topic_id));
        exit;
    } else {
        echo "<p style='color: red;'>Veuillez écrire un message.</p>";
    }
}

// 3) Récupérer tous les posts de ce topic
$sql = "SELECT p.post_id, p.message, p.created_at, u.username
        FROM forum_posts p
        JOIN users u ON p.user_id = u.user_id
        WHERE p.topic_id = :topic_id
        ORDER BY p.created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->execute([':topic_id' => $topic_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Discussion : <?= htmlspecialchars($topic['title']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include('header.php'); ?>

<div class="container">
    <h1><?= htmlspecialchars($topic['title']); ?></h1>
    <p>
        Créé par <strong><?= htmlspecialchars($topic['username']); ?></strong>
        le <?= $topic['created_at']; ?>
    </p>

    <hr>

    <h2>Messages</h2>
    <?php if (empty($posts)): ?>
        <p>Aucun message pour le moment. Soyez le premier à répondre !</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="message" style="margin-bottom: 15px;">
                <p>
                    <strong><?= htmlspecialchars($post['username']); ?></strong>
                    <span class="msg-time">[<?= $post['created_at']; ?>]</span>
                </p>
                <p><?= nl2br(htmlspecialchars($post['message'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>

    <h2>Répondre</h2>
    <form action="" method="post">
        <textarea name="message" rows="4" style="width: 100%;" required></textarea><br><br>
        <button type="submit">Envoyer</button>
    </form>
</div>

</body>
</html>
