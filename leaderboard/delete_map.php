<?php
session_start();
include('conLeaderboard.php'); // Inclut la connexion PDO

// Vérification du rôle administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: leaderboard.php");
    exit;
}

$map_id = $_GET['map_id'] ?? null;
if (!$map_id) {
    header("Location: leaderboard.php");
    exit;
}

// 1) Supprimer les scores associés
$stmt = $conn->prepare("DELETE FROM leaderboardentries WHERE map_id = :map_id");
$stmt->execute(['map_id' => $map_id]);

// 2) Supprimer les messages associés
$stmt = $conn->prepare("DELETE FROM map_messages WHERE map_id = :map_id");
$stmt->execute(['map_id' => $map_id]);

// 3) Supprimer la map
$stmt = $conn->prepare("DELETE FROM maps WHERE map_id = :map_id");
$stmt->execute(['map_id' => $map_id]);

// Rediriger vers l'accueil (leaderboard.php) après suppression
header("Location: leaderboard.php");
exit;
?>
