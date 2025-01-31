<?php
include('../JeuXML/con.php');

$creators = [];
$players = [];

// Récupérer les créateurs
$sql = "SELECT DISTINCT user_id, username FROM users";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $creators[] = $row;
}

// Récupérer les joueurs ayant un temps enregistré
$sql = "SELECT DISTINCT u.user_id, u.username FROM users u JOIN leaderboardentries le ON u.user_id = le.user_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $players[] = $row;
}

$conn->close();
echo json_encode(["creators" => $creators, "players" => $players]);
?>
