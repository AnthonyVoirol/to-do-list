<?php
session_start();

header("Content-Type: application/json");

if (!empty($_FILES['avatar'])) {

    $allowedTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/svg+xml'
    ];

    $fileType = $_FILES['avatar']['type'];

    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(["error" => "Seules les images sont acceptées"]);
        exit;
    }

    $fileExt = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    
    if (!in_array($fileExt, $allowedExtensions)) {
        http_response_code(400);
        echo json_encode(["error" => "Format d'image non supporté"]);
        exit;
    }

    if (!isset($_SESSION['username'])) {
        http_response_code(401);
        echo json_encode(["error" => "Session expirée"]);
        exit;
    }

    $targetDir = "../avatars/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = $_SESSION["username"] . ".png";
    $targetFile = $targetDir . $fileName;

    if ($fileExt !== 'png') {
        try {
            switch ($fileExt) {
                case 'jpg':
                case 'jpeg':
                    $sourceImage = imagecreatefromjpeg($_FILES['avatar']['tmp_name']);
                    break;
                case 'gif':
                    $sourceImage = imagecreatefromgif($_FILES['avatar']['tmp_name']);
                    break;
                case 'webp':
                    $sourceImage = imagecreatefromwebp($_FILES['avatar']['tmp_name']);
                    break;
                case 'bmp':
                    $sourceImage = imagecreatefrombmp($_FILES['avatar']['tmp_name']);
                    break;
                default:
                    throw new Exception("Format non supporté pour la conversion");
            }

            if ($sourceImage === false) {
                throw new Exception("Impossible de lire l'image");
            }

            imagepng($sourceImage, $targetFile, 9);
            imagedestroy($sourceImage);

            $_SESSION['avatar'] = $_SESSION['username'];

            $publicPath = "../avatars/" . $fileName;

            echo json_encode([
                "success" => true,
                "newPath" => $publicPath
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la conversion: " . $e->getMessage()]);
        }
    } else {
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
            
            $_SESSION['avatar'] = $_SESSION['username'];

            $publicPath = "../avatars/" . $fileName;

            echo json_encode([
                "success" => true,
                "newPath" => $publicPath
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Échec de l'upload"]);
        }
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Aucun fichier reçu"]);
}
?>