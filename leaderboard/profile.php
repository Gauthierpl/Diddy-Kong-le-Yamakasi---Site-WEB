<?php
session_start();
include('conLeaderboard.php'); // Ton script de connexion à la BDD

// Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupérer les éventuels messages flash
$flashSuccess = $_SESSION['flash_success'] ?? '';
$flashError   = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Charger les infos de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql = "SELECT user_id, username, account_creation_date, total_bananes, role, avatar
        FROM users
        WHERE user_id = :uid
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':uid' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="profile.css">
</head>
<body>

<?php include('header.php'); ?>

<div class="profile-container">
    <h1>Mon Profil</h1>

    <!-- Affichage des messages flash -->
    <?php if (!empty($flashSuccess)): ?>
        <div class="profile-alert success">
            <?= htmlspecialchars($flashSuccess); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($flashError)): ?>
        <div class="profile-alert error">
            <?= htmlspecialchars($flashError); ?>
        </div>
    <?php endif; ?>

    <!-- Affichage des infos utilisateur -->
    <div class="profile-info">
        <p><strong>ID :</strong> <?= htmlspecialchars($user['user_id']); ?></p>
        <p><strong>Pseudo :</strong> <?= htmlspecialchars($user['username']); ?></p>
        <p><strong>Date de création :</strong> <?= htmlspecialchars($user['account_creation_date']); ?></p>
        <p><strong>Total Bananes :</strong> <?= htmlspecialchars($user['total_bananes']); ?></p>
        <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']); ?></p>

        <?php if (!empty($user['avatar'])): ?>
            <p><strong>Avatar actuel :</strong></p>
            <img src="<?= htmlspecialchars($user['avatar']); ?>" alt="Avatar" style="max-width: 100px;">
        <?php else: ?>
            <p><em>Aucun avatar</em></p>
        <?php endif; ?>
    </div>

    <!-- Formulaire de modification du profil -->
    <h2>Modifier mon profil</h2>
    <form action="redim_image_profile.php" method="post" class="profile-form" enctype="multipart/form-data">
        <label for="username">Nouveau pseudo :</label>
        <input type="text" id="username" name="username" 
               value="<?= htmlspecialchars($user['username']); ?>" required>

        <label for="new_password">Nouveau mot de passe (optionnel) :</label>
        <input type="password" id="new_password" name="new_password">

        <label for="confirm_password">Confirmer le mot de passe :</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <label for="avatar">Nouvel avatar (optionnel) :</label>
        <input type="file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png">

        <!-- Canvas de preview -->
        <div class="preview-container">
            <canvas id="avatarPreview" width="100" height="100"
                    style="display: none; border: 2px solid #00ff00;"></canvas>
        </div>

        <button type="submit">Enregistrer</button>
    </form>
</div>

<!-- Script JavaScript pour l'aperçu avant upload -->
<script src="profile.js"></script>

</body>
</html>
