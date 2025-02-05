<?php
include('conLeaderboard.php');
include('models/models.php');

$filters = getFilters($conn);

header('Content-Type: application/json');
echo json_encode($filters);
?>
