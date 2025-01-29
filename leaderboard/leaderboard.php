<?php
// Informations de connexion
$host = "localhost"; // Hôte, souvent "localhost" sur Hostinger
$username = "u553586303_8kJ5f82H87DW7Y"; // Nom d'utilisateur MySQL complet
$password = "WK6pey29x4V7FLb93ufT"; // Mot de passe MySQL
$database = "u553586303_JeuXML"; // Nom complet de la base de données

// Création de la connexion
$conn = new mysqli($host, $username, $password, $database);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// echo "Connexion réussie"; // Pour tester



// Récupérer toutes les maps et les classements
$sql = "
    SELECT m.map_id, m.map_code, m.screenshot_path, u.username, le.time
    FROM maps m
    LEFT JOIN leaderboardentries le ON m.map_id = le.map_id
    LEFT JOIN users u ON le.user_id = u.user_id
    ORDER BY m.map_id, le.time ASC
";
$result = $conn->query($sql);

$maps = [];
while ($row = $result->fetch_assoc()) {
    $maps[$row['map_id']]['map_code'] = $row['map_code'];
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .map-container {
            display: flex;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .map-container img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-right: 20px;
        }
        .map-info {
            flex: 1;
        }
        .map-info h2 {
            margin: 0;
            font-size: 24px;
        }
        .leaderboard {
            margin-top: 10px;
        }
        .leaderboard table {
            width: 100%;
            border-collapse: collapse;
        }
        .leaderboard table, .leaderboard th, .leaderboard td {
            border: 1px solid #ddd;
        }
        .leaderboard th, .leaderboard td {
            padding: 8px;
            text-align: left;
        }
        .leaderboard th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Leaderboard des Maps</h1>

    <?php foreach ($maps as $map_id => $map): ?>
        <div class="map-container">
            <img src="<?php echo $map['screenshot']; ?>" alt="Map screenshot">
            <div class="map-info">
                <h2><?php echo htmlspecialchars($map['map_code']); ?></h2>
                <div class="leaderboard">
                    <h3>Classement :</h3>
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
                                    <td><?php echo htmlspecialchars($entry['username']); ?></td>
                                    <td><?php echo number_format($entry['time'], 3); ?> sec</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
