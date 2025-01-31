<?php
// Inclure la connexion à la base de données
include('../JeuXML/con.php');
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

    <!-- Filtres -->
    <div class="filters">
        <label for="creator">Filtrer par créateur :</label>
        <select id="creator">
            <option value="">Tous</option>
            <!-- Options ajoutées dynamiquement -->
        </select>

        <label for="player">Filtrer par joueur :</label>
        <select id="player">
            <option value="">Tous</option>
            <!-- Options ajoutées dynamiquement -->
        </select>
    </div>

    <!-- Conteneur où s'affichent les maps -->
    <div id="leaderboard-container">
        <!-- Les maps seront chargées ici via AJAX -->
    </div>
</div>

<script src="script.js"></script>

</body>
</html>
