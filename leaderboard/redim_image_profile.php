<?php
session_start();
include('conLeaderboard.php'); // Connexion à la BDD

// Vérifier connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// On prépare des variables pour messages
$errorMessage   = "";
$successMessage = "";

// 1) Gérer PSEUDO + MOT DE PASSE
$new_username = trim($_POST['username'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if (empty($new_username)) {
    $errorMessage = "Le pseudo ne peut pas être vide.";
} else {
    if (!empty($new_password) || !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            $errorMessage = "Les mots de passe ne correspondent pas.";
        } else {
            // Mettre à jour pseudo + mdp
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users
                    SET username = :username, password = :password
                    WHERE user_id = :uid";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':username' => $new_username,
                ':password' => $hashedPassword,
                ':uid'      => $user_id
            ]);
            $_SESSION['username'] = $new_username;
            $successMessage = "Profil mis à jour (pseudo + mot de passe).";
        }
    } else {
        // Juste pseudo
        $sql = "UPDATE users
                SET username = :username
                WHERE user_id = :uid";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':username' => $new_username,
            ':uid'      => $user_id
        ]);
        $_SESSION['username'] = $new_username;
        $successMessage = "Profil mis à jour (pseudo).";
    }
}

// 2) Gérer l'upload d'avatar (si pas d'erreur jusque-là)
if (empty($errorMessage) && isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $tmpName   = $_FILES['avatar']['tmp_name'];
        $fileName  = $_FILES['avatar']['name'];
        $fileSize  = $_FILES['avatar']['size'];

        // **Correction : Définir les formats autorisés**
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $allowedMime = ['image/jpeg', 'image/png'];

        // Extraire extension
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $imageInfo = getimagesize($tmpName);
        $mime = $imageInfo['mime'] ?? '';

        if (!in_array($extension, $allowedExtensions) || !in_array($mime, $allowedMime)) {
            $errorMessage = "Seuls les fichiers JPG, JPEG et PNG sont autorisés.";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $errorMessage = "Fichier trop volumineux (max 2Mo).";
        } else {
            if (!$imageInfo) {
                $errorMessage = "Le fichier n'est pas une image valide.";
            } else {
                // OK => Redimensionner en 100x100 via GD
                $uploadDir = __DIR__ . "/images/profile_images";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }
                
                // Nom unique
                $newFileName = "user_{$user_id}_" . uniqid() . "." . $extension;
                $destination = $uploadDir . "/" . $newFileName;

                // Charger la source selon le type
                switch ($mime) {
                    case 'image/png':
                        $srcImg = imagecreatefrompng($tmpName);
                        break;
                    default:
                        $srcImg = imagecreatefromjpeg($tmpName);
                        break;
                }

                if (!$srcImg) {
                    $errorMessage = "Erreur lors de la création de l'image GD.";
                } else {
                    // Créer un canevas 100x100
                    $dstImg = imagecreatetruecolor(100, 100);
                    imagecopyresampled(
                        $dstImg, $srcImg,
                        0, 0, 0, 0,
                        100, 100,
                        imagesx($srcImg), imagesy($srcImg)
                    );

                    // Sauvegarde du fichier redimensionné
                    if ($mime === 'image/png') {
                        imagepng($dstImg, $destination);
                    } else {
                        imagejpeg($dstImg, $destination, 90);
                    }

                    imagedestroy($srcImg);
                    imagedestroy($dstImg);

                    // Mettre à jour le chemin en base
                    $avatarPath = "images/profile_images/" . $newFileName;
                    $sql = "UPDATE users
                            SET avatar = :avatar
                            WHERE user_id = :uid";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        ':avatar' => $avatarPath,
                        ':uid'    => $user_id
                    ]);

                    $successMessage .= "<br>Avatar redimensionné en 100x100 et mis à jour !";
                }
            }
        }
    } else {
        $errorMessage = "Erreur d'upload : code " . $_FILES['avatar']['error'];
    }
}

// 3) Post/Redirect/Get : rediriger vers profile.php pour éviter les doubles uploads
if (!empty($errorMessage)) {
    $_SESSION['flash_error'] = $errorMessage;
} else {
    $_SESSION['flash_success'] = !empty($successMessage) ? $successMessage : "Profil mis à jour !";
}

header("Location: profile.php");
exit;
