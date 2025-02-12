<?php
// Inclure la connexion à la base de données
include('conLeaderboard.php');

/**
 * Récupère les maps filtrées par créateur et joueur
 */
function getFilteredMaps($conn, $creator, $player) {
    $sql = "SELECT DISTINCT m.map_id 
            FROM maps m 
            LEFT JOIN leaderboardentries le ON m.map_id = le.map_id 
            WHERE 1";
    
    $params = [];

    if (!empty($creator)) {
        $sql .= " AND m.user_id = :creator";
        $params[':creator'] = $creator;
    }

    if (!empty($player)) {
        $sql .= " AND le.user_id = :player";
        $params[':player'] = $player;
    }

    $stmt = $conn->prepare($sql);

    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
}

/**
 * Récupère les détails des maps filtrées
 */
function getLeaderboardMaps($conn, $filteredMapIds) {
    if (empty($filteredMapIds)) {
        return [];
    }

    // Créer des placeholders pour chaque map_id
    $placeholders = implode(',', array_fill(0, count($filteredMapIds), '?'));

    $sql = "SELECT m.map_id, m.screenshot_path, u.username AS creator, 
                   le.user_id, p.username AS player, le.time
            FROM maps m
            LEFT JOIN users u ON m.user_id = u.user_id
            LEFT JOIN leaderboardentries le ON m.map_id = le.map_id
            LEFT JOIN users p ON le.user_id = p.user_id
            WHERE m.map_id IN ($placeholders)
            ORDER BY m.map_id, le.time ASC";

    $stmt = $conn->prepare($sql);

    // Passer les IDs comme valeurs pour exécution
    $stmt->execute($filteredMapIds);

    $maps = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $map_id = $row['map_id'];

        if (!isset($maps[$map_id])) {
            $maps[$map_id] = [
                'map_id' => $row['map_id'],
                'screenshot' => $row['screenshot_path'],
                'creator' => $row['creator'],
                'entries' => []
            ];
        }

        if (!empty($row['player'])) {
            $maps[$map_id]['entries'][] = [
                'username' => $row['player'],
                'time' => $row['time']
            ];
        }
    }

    return $maps;
}


/**
 * Récupère les créateurs et les joueurs
 */
function getFilters($conn) {
    $filters = ['creators' => [], 'players' => []];

    $stmt = $conn->prepare("SELECT DISTINCT user_id, username FROM users");
    $stmt->execute();
    $filters['creators'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT DISTINCT u.user_id, u.username FROM users u JOIN leaderboardentries le ON u.user_id = le.user_id");
    $stmt->execute();
    $filters['players'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $filters;
}
?>
