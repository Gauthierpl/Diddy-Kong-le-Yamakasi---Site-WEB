<?php
session_start(); // 💡 Assure que la session est bien active

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('conLeaderboard.php');

$map_id = $_GET['map_id'] ?? null;

if (!$map_id) {
    echo "ID de la map manquant.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Discussion de la Map <?= htmlspecialchars($map_id); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include('header.php'); ?> <!-- 💡 Maintenant, le header est bien inclus -->

<div class="container">
    <h1>Discussion de la Map <?= htmlspecialchars($map_id); ?></h1>

    <div id="messages-container"></div>

    <form id="chat-form">
        <input type="hidden" id="map_id" value="<?= htmlspecialchars($map_id); ?>">
        <textarea id="message" placeholder="Écris un message..." required></textarea>
        <button type="submit">Envoyer</button>
    </form>
</div>

<script src="messages.js"></script>

</body>
</html>
