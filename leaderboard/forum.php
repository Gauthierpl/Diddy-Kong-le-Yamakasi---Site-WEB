<?php
session_start();
include('conLeaderboard.php'); // PDO
// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Forum</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include('header.php'); ?>

<div class="container">
    <h1>Forum de discussion</h1>

    <?php
    // 1) Traiter le formulaire de création de topic
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
        $title = trim($_POST['title']);
        if (!empty($title)) {
            $stmt = $conn->prepare("INSERT INTO forum_topics (user_id, title) VALUES (:user_id, :title)");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':title'   => $title
            ]);
            echo "<p style='color: green;'>Discussion créée avec succès !</p>";
        } else {
            echo "<p style='color: red;'>Veuillez entrer un titre.</p>";
        }
    }

    // 2) Lister les topics
    $sql = "SELECT t.topic_id, t.title, t.created_at, u.username
            FROM forum_topics t
            JOIN users u ON t.user_id = u.user_id
            ORDER BY t.created_at DESC";
    $stmt = $conn->query($sql);
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- Formulaire pour créer un nouveau topic -->
    <form action="" method="post" style="margin-bottom: 20px;">
        <label for="title">Créer une nouvelle discussion :</label><br>
        <input type="text" id="title" name="title" style="width: 300px;" required>
        <button type="submit">Créer</button>
    </form>

    <h2>Liste des discussions</h2>
    <?php if (empty($topics)): ?>
        <p>Aucune discussion pour le moment.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($topics as $topic): ?>
                <li>
                    <a href="view_topic.php?topic_id=<?= $topic['topic_id']; ?>">
                        <?= htmlspecialchars($topic['title']); ?>
                    </a>
                    <br>
                    <small>
                        Créé par <?= htmlspecialchars($topic['username']); ?>
                        le <?= $topic['created_at']; ?>
                    </small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</div>

</body>
</html>
