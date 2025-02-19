<?php
session_start();
include('conLeaderboard.php'); // Connexion PDO

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fonction utilitaire pour formater la date en français
function formatFrenchDate($dateString) {
    // Méthode 1 (avec setlocale + strftime) :
    // setlocale(LC_TIME, 'fr_FR.UTF-8');
    // return strftime("%e %B %Y à %H:%M", strtotime($dateString));

    // Méthode 2 (tableau de mois) : plus sûre si la locale fr_FR n’est pas dispo
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

// Traitement du formulaire de création de topic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $conn->prepare("INSERT INTO forum_topics (user_id, title) VALUES (:user_id, :title)");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':title'   => $title
        ]);
        $successMessage = "Discussion créée avec succès !";
    } else {
        $errorMessage = "Veuillez entrer un titre.";
    }
}

// Récupération de la liste des topics
$sql = "SELECT t.topic_id, t.title, t.created_at, u.username
        FROM forum_topics t
        JOIN users u ON t.user_id = u.user_id
        ORDER BY t.created_at DESC";
$stmt = $conn->query($sql);
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Forum</title>
    <!-- On ajoute un nouveau fichier CSS spécifique au forum -->
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="forum.css">
</head>
<body>

<?php include('header.php'); ?>

<div class="forum-container">
    <h1>Forum de discussion</h1>

    <!-- Affichage des messages de réussite / erreur -->
    <?php if (!empty($successMessage)): ?>
        <div class="forum-alert success">
            <?= htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="forum-alert error">
            <?= htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire pour créer un nouveau topic -->
    <div class="forum-new-topic">
        <form action="" method="post">
            <label for="title">Créer une nouvelle discussion :</label><br>
            <input type="text" id="title" name="title" required>
            <button type="submit">Créer</button>
        </form>
    </div>

    <!-- Liste des discussions -->
    <h2>Liste des discussions</h2>
    <?php if (empty($topics)): ?>
        <p>Aucune discussion pour le moment.</p>
    <?php else: ?>
        <ul class="forum-topic-list">
            <?php foreach ($topics as $topic): ?>
                <li class="forum-topic-item">
                    <a class="topic-title" href="view_topic.php?topic_id=<?= $topic['topic_id']; ?>">
                        <?= htmlspecialchars($topic['title']); ?>
                    </a>
                    <div class="topic-meta">
                        Créé par <strong><?= htmlspecialchars($topic['username']); ?></strong>
                        <br>
                        <!-- Formatage de la date en français -->
                        Discussion créée le <?= formatFrenchDate($topic['created_at']); ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

</body>
</html>
