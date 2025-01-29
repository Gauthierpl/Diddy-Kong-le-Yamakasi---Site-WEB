<?php
// Informations de connexion
$host = "localhost"; // Hôte, souvent "localhost" sur Hostinger
$username = "u553586303_8kJ5f82H87DW7Y"; // Nom d'utilisateur MySQL complet
$password = "WK6pey29x4V7FLb93ufT"; // Mot de passe MySQL
$database = "u553586303_JeuXML"; // Nom complet de la base de données

// Création de la connexion
$conn = new mysqli($host, $username, $password, $database);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// echo "Connexion réussie"; // Pour tester
?>
