<?php
include('../JeuXML/con.php');

// Récupérer les filtres
$creator = isset($_GET['creator']) ? $_GET['creator'] : '';
$player = isset($_GET['player']) ? $_GET['player'] : '';

// Étape 1 : Trouver les maps correspondant aux filtres
$sql = "SELECT DISTINCT m.map_id FROM maps m LEFT JOIN leaderboardentries le ON m.map_id = le.map_id WHERE 1";

if (!empty($creator)) {
    $sql .= " AND m.user_id = '$creator'";
}

if (!empty($player)) {
    $sql .= " AND le.user_id = '$player'";
}

$result = $conn->query($sql);
$filtered_map_ids = [];

while ($row = $result->fetch_assoc()) {
    $filtered_map_ids[] = $row['map_id'];
}

// Si aucun résultat, afficher un message et arrêter l'exécution
if (!empty($creator) && empty($filtered_map_ids)) {
    echo "<p class='no-results'>Aucune map trouvée pour ce créateur.</p>";
    exit;
}
if (!empty($player) && empty($filtered_map_ids)) {
    echo "<p class='no-results'>Aucune map trouvée où ce joueur possède un temps.</p>";
    exit;
}

// Étape 2 : Charger les classements des maps filtrées
$maps = [];
$sql = "
    SELECT m.map_id, m.screenshot_path, u.username AS creator, le.user_id, p.username AS player, le.time
    FROM maps m
    LEFT JOIN users u ON m.user_id = u.user_id
    LEFT JOIN leaderboardentries le ON m.map_id = le.map_id
    LEFT JOIN users p ON le.user_id = p.user_id
    WHERE 1
";

if (!empty($filtered_map_ids)) {
    $sql .= " AND m.map_id IN (" . implode(',', $filtered_map_ids) . ")";
}

$sql .= " ORDER BY m.map_id, le.time ASC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $maps[$row['map_id']]['map_id'] = $row['map_id'];
    $maps[$row['map_id']]['screenshot'] = $row['screenshot_path'];
    $maps[$row['map_id']]['creator'] = $row['creator'];
    $maps[$row['map_id']]['entries'][] = [
        'username' => $row['player'],
        'time' => $row['time']
    ];
}

$conn->close();

// Générer le HTML
foreach ($maps as $map_id => $map): ?>
    <div class="map-container">
        <div class="map-id"><?php echo htmlspecialchars($map['map_id']); ?></div>
        <div class="map-content">
            <?php
            $image_path = "https://gauthierpl.fr/JeuXML/" . ltrim(htmlspecialchars($map['screenshot']), '/');
            ?>
            <img src="<?php echo $image_path; ?>" alt="Map Image">
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
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($entry['username'] ?? "Inconnu"); ?></td>
                                    <td><?php echo number_format($entry['time'] ?? 0, 3); ?> sec</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
