<?php
// Inclure la connexion à la base de données
include('../JeuXML/con.php');

// Récupérer toutes les maps et les classements
$sql = "
    SELECT m.map_id, m.screenshot_path, u.username, le.time
    FROM maps m
    LEFT JOIN leaderboardentries le ON m.map_id = le.map_id
    LEFT JOIN users u ON le.user_id = u.user_id
    ORDER BY m.map_id, le.time ASC
";
$result = $conn->query($sql);

$maps = [];
while ($row = $result->fetch_assoc()) {
    $maps[$row['map_id']]['map_id'] = $row['map_id'];
    $maps[$row['map_id']]['screenshot'] = $row['screenshot_path'];
    $maps[$row['map_id']]['entries'][] = [
        'username' => $row['username'],
        'time' => $row['time']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Leaderboard des Maps</h1>

    <?php foreach ($maps as $map_id => $map): ?>
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
</div>

</body>
</html>
