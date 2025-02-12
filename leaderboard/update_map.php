<?php
session_start();
include('conLeaderboard.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: leaderboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['map_id']) && isset($_POST['times'])) {
    $map_id = $_POST['map_id'];
    $times = $_POST['times'];

    foreach ($times as $username => $new_time) {
        // Récupérer l'ID du joueur
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user_id = $stmt->fetchColumn();

        if ($user_id) {
            // Mettre à jour le temps
            $update_stmt = $conn->prepare("UPDATE leaderboardentries SET time = :time WHERE map_id = :map_id AND user_id = :user_id");
            $update_stmt->bindParam(':time', $new_time);
            $update_stmt->bindParam(':map_id', $map_id);
            $update_stmt->bindParam(':user_id', $user_id);
            $update_stmt->execute();
        }
    }

    header("Location: modifier_map.php?map_id=" . urlencode($map_id));
    exit;
}

header("Location: leaderboard.php");
exit;
?>
