<?php
session_start();
?>

<header class="main-header">
    <div class="header-container">
        <!-- Titre à gauche (si tu veux) -->
        <div class="site-title">Mon Leaderboard</div>

        <nav class="nav-links">
            <a href="leaderboard.php">Accueil</a>
            
            <?php if (!isset($_SESSION['username'])): ?>
                <!-- Pas connecté = lien de connexion -->
                <a href="login.php">Se Connecter</a>
            <?php else: ?>
                <!-- Connecté = Bonjour + Se Déconnecter -->
                <span class="welcome">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <!-- Si admin, on indique qu'il est admin -->
                    <span style="color: #ff007f; margin-left: 10px;">
                        (Vous êtes Admin)
                    </span>
                <?php else: ?>
                    <!-- Sinon, joueur -->
                    <span style="color: #00ff00; margin-left: 10px;">
                        (Vous êtes un joueur)
                    </span>
                <?php endif; ?>

                <a href="logout.php">Se Déconnecter</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
