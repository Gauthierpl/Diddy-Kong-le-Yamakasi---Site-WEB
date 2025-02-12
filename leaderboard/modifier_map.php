<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('conLeaderboard.php');
include('models/models.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: leaderboard.php");
    exit;
}

$map_id = $_GET['map_id'] ?? null;

if (!$map_id) {
    echo "ID de la map manquant.";
    exit;
}

// Charger les infos de la map
$maps = getLeaderboardMaps($conn, [$map_id]);
$map = $maps[$map_id] ?? null;

if (!$map) {
    echo "Cette map n'existe pas.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Map</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include('header.php'); ?>

<div class="container">
    <h1>Modification de la Map <?= htmlspecialchars($map_id); ?></h1>

    <div class="map-container">
        <div class="map-header">
            <div class="map-id"><?= htmlspecialchars($map_id); ?></div>
        </div>

        <div class="map-content">
            <img src="<?= htmlspecialchars("https://gauthierpl.fr/JeuXML/" . ltrim($map['screenshot'], '/')); ?>" alt="Map Image">
            
            <div class="map-info">
                <form action="update_map.php" method="POST">
                    <input type="hidden" name="map_id" value="<?= htmlspecialchars($map_id); ?>">

                    <div class="leaderboard">
                        <table>
                            <thead>
                                <tr>
                                    <th>Joueur</th>
                                    <th>Temps (en sec)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($map['entries'] as $entry): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($entry['username']); ?></td>
                                        <td>
                                            <input type="number" step="0.001" name="times[<?= htmlspecialchars($entry['username']); ?>]" 
                                                value="<?= htmlspecialchars($entry['time']); ?>">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="save-button">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
