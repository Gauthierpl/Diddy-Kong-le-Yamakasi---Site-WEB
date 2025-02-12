<?php
session_start();
include('conLeaderboard.php'); // Ton fichier de connexion (PDO ou MySQLi)

// Message d'erreur (vide au départ)
$error_message = "";

// Si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer pseudo et password
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Vérifier qu'ils ne sont pas vides
    if (empty($username) || empty($password)) {
        $error_message = "Veuillez entrer votre pseudo et votre mot de passe.";
    } else {
        // Requête préparée pour éviter l'injection SQL
        $sql = "SELECT user_id, username, password, role
                FROM users
                WHERE username = :username
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vérification du mot de passe (hashé en BDD)
            if (password_verify($password, $user['password'])) {
                // Stocker en session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];  // Récupère le rôle depuis la BDD

                // Rediriger vers la page d'accueil (leaderboard.php) ou où tu veux
                header("Location: leaderboard.php");
                exit;
            } else {
                $error_message = "Pseudo ou mot de passe incorrect.";
            }
        } else {
            $error_message = "Pseudo ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css"> <!-- Le même style que d’habitude -->
</head>
<body>
<div class="container">
    <h1>Connexion</h1>
    
    <!-- Affiche un message d'erreur si besoin -->
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form action="" method="POST">
        <label for="username">Pseudo :</label><br>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">Mot de passe :</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>
