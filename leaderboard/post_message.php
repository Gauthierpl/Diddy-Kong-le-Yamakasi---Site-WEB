<?php
session_start();
include('conLeaderboard.php');

if (!isset($_SESSION['user_id'])) {
    exit;
}

$map_id = $_POST['map_id'];
$message = trim($_POST['message']);

if (!empty($message)) {
    $stmt = $conn->prepare("INSERT INTO map_messages (map_id, user_id, message) VALUES (:map_id, :user_id, :message)");
    $stmt->bindValue(':map_id', $map_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':message', $message, PDO::PARAM_STR);
    $stmt->execute();
}
?>
