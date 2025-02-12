<?php
$config = include('config.php'); // Inclure le fichier sécurisé




try {
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Gérer les erreurs en mode exception
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Mode par défaut : tableau associatif
        PDO::ATTR_EMULATE_PREPARES => false, // Sécurité contre les injections SQL
    ];

    $conn = new PDO($dsn, $config['username'], $config['password'], $options);

} catch (PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage()); // Enregistre l'erreur dans les logs
    die("Une erreur est survenue. Veuillez réessayer plus tard."); // Pas d'affichage d'erreur en prod
}
?>
