<?php
include('conLeaderboard.php');
include('models/models.php');
session_start();

$creator = $_GET['creator'] ?? '';
$player = $_GET['player'] ?? '';

// Étape 1 : Récupérer les maps filtrées
$filteredMapIds = getFilteredMaps($conn, $creator, $player);

// Étape 2 : Charger les classements des maps filtrées
$maps = getLeaderboardMaps($conn, $filteredMapIds);

// Génération du HTML sécurisé
foreach ($maps as $map_id => $map): ?>
    <div class="map-container">
        <div class="map-header">
            <div class="map-id"><?= htmlspecialchars($map['map_id']); ?></div>
            
            <!-- Bouton "Modifier" pour les admins -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="modifier_map.php?map_id=<?= $map['map_id']; ?>" class="edit-button">
                    Modifier
                </a>
            <?php endif; ?>

            <!-- Bouton "Discussion" pour les joueurs -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'player'): ?>
                <a href="discussion_map.php?map_id=<?= $map['map_id']; ?>" class="chat-button">
                    Discussion
                </a>
            <?php endif; ?>
        </div>

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
