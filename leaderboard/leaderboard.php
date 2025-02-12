<?php
include('conLeaderboard.php'); // Inclure la connexion à la base de données
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Ton fichier de style -->
</head>
<body>

<?php include('header.php'); ?> 
<!-- Ton header, qui gère le bouton Se Connecter / Se Déconnecter / admin ... -->

<div class="container">
    <h1>Leaderboard des Maps</h1>

    <!-- Messages différents selon le rôle de l'utilisateur -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <p style="color: #ff007f;">
            Ici, tu peux gérer les temps, supprimer des maps, etc. (admin seulement)
        </p>
    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'player'): ?>
        <p style="color: #00ff00;">
            Ici, tu vois un contenu réservé aux joueurs connectés (mais pas admin).
        </p>
    <?php else: ?>
        <p>
            Tu es invité, connecte-toi pour accéder à plus de fonctionnalités.
        </p>
    <?php endif; ?>

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
