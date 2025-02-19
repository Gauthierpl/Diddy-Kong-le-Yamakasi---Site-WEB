<?php
session_start();
include('conLeaderboard.php'); // Connexion PDO

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// topic_id dans l'URL
$topic_id = $_GET['topic_id'] ?? null;
if (!$topic_id) {
    echo "Topic introuvable.";
    exit;
}

// Même fonction utilitaire pour la date
function formatFrenchDate($dateString) {
    // setlocale(LC_TIME, 'fr_FR.UTF-8');
    // return strftime("%e %B %Y à %H:%M", strtotime($dateString));

    $mois = [
        '01' => 'janvier', '02' => 'février', '03' => 'mars',      '04' => 'avril',
        '05' => 'mai',     '06' => 'juin',    '07' => 'juillet',   '08' => 'août',
        '09' => 'septembre','10' => 'octobre','11' => 'novembre',  '12' => 'décembre'
    ];
    $time = strtotime($dateString);
    $jour = date('d', $time);
    $moisNum = date('m', $time);
    $annee = date('Y', $time);
    $heure = date('H:i', $time);

    return "$jour {$mois[$moisNum]} $annee à $heure";
}

// Vérifier que le topic existe
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

// Gérer l'envoi d'un nouveau message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO forum_posts (topic_id, user_id, message) VALUES (:topic_id, :user_id, :message)");
        $stmt->execute([
            ':topic_id' => $topic_id,
            ':user_id'  => $_SESSION['user_id'],
            ':message'  => $message
        ]);
        // Pour éviter la double soumission en rafraîchissant la page
        header("Location: view_topic.php?topic_id=" . urlencode($topic_id));
        exit;
    } else {
        $errorMessage = "Veuillez écrire un message.";
    }
}

// Récupérer la liste des posts
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
    <title><?= htmlspecialchars($topic['title']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="forum.css">
</head>
<body>

<?php include('header.php'); ?>

<div class="forum-container">
    <h1><?= htmlspecialchars($topic['title']); ?></h1>
    <div class="topic-meta">
        Discussion créée par <strong><?= htmlspecialchars($topic['username']); ?></strong><br>
        Le <?= formatFrenchDate($topic['created_at']); ?>
    </div>

    <hr>

    <h2>Messages</h2>

    <!-- Message d'erreur éventuel -->
    <?php if (!empty($errorMessage)): ?>
        <div class="forum-alert error">
            <?= htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <p>Aucun message pour le moment. Soyez le premier à répondre !</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="forum-post">
                <p class="post-header">
                    <strong><?= htmlspecialchars($post['username']); ?></strong>
                    <span class="post-date">[<?= formatFrenchDate($post['created_at']); ?>]</span>
                </p>
                <p class="post-message"><?= nl2br(htmlspecialchars($post['message'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>

    <h2>Répondre</h2>
    <form action="" method="post" class="forum-reply-form">
        <textarea name="message" rows="4" required></textarea><br>
        <button type="submit">Envoyer</button>
    </form>
</div>

</body>
</html>
