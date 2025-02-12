<?php
include('conLeaderboard.php');

$map_id = $_GET['map_id'] ?? null;

if (!$map_id) {
    exit;
}

$sql = "SELECT mm.message, mm.created_at, u.username 
        FROM map_messages mm
        JOIN users u ON mm.user_id = u.user_id
        WHERE mm.map_id = :map_id
        ORDER BY mm.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':map_id', $map_id, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);
?>
