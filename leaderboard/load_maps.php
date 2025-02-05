<?php
include('conLeaderboard.php');
include('models/models.php');



$creator = $_GET['creator'] ?? '';
$player = $_GET['player'] ?? '';

// Étape 1 : Récupérer les maps filtrées
$filteredMapIds = getFilteredMaps($conn, $creator, $player);


// Étape 2 : Charger les classements des maps filtrées
$maps = getLeaderboardMaps($conn, $filteredMapIds);



// Génération du HTML sécurisé
foreach ($maps as $map_id => $map): ?>
    <div class="map-container">
        <div class="map-id"><?= htmlspecialchars($map['map_id']); ?></div>
        <div class="map-content">
            <img src="<?= htmlspecialchars("https://gauthierpl.fr/JeuXML/" . ltrim($map['screenshot'], '/')); ?>" alt="Map Image">
            <div class="map-info">
                <div class="leaderboard">
                    <table>
                        <thead>
                            <tr>
                                <th>Rang</th>
                                <th>Joueur</th>
                                <th>Temps</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($map['entries'] as $index => $entry): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= htmlspecialchars($entry['username'] ?? "Inconnu"); ?></td>
                                    <td><?= number_format($entry['time'] ?? 0, 3); ?> sec</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endforeach;
?>
