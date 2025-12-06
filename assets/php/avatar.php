<?php
session_start();
require_once 'dbConfig.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Session expirée"]);
    exit;
}

if (empty($_FILES['avatar'])) {
    http_response_code(400);
    echo json_encode(["error" => "Aucun fichier reçu"]);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
$fileType = $_FILES['avatar']['type'];

if (!in_array($fileType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(["error" => "Seules les images sont acceptées"]);
    exit;
}

$fileExt = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

if (!in_array($fileExt, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode(["error" => "Format d'image non supporté"]);
    exit;
}

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(500);
    echo json_encode(["error" => "Utilisateur introuvable"]);
    exit;
}

$username = $user['username'];
$targetDir = "../avatars/";

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$fileName = $username . ".png";
$targetFile = $targetDir . $fileName;

if (file_exists($targetFile)) {
    unlink($targetFile);
    // Attendre un peu pour s'assurer que le fichier est bien supprimé
    clearstatcache(true, $targetFile);
}

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
        case 'png':
            $sourceImage = imagecreatefrompng($_FILES['avatar']['tmp_name']);
            break;
        default:
            throw new Exception("Format non supporté");
    }

    if ($sourceImage === false) {
        throw new Exception("Impossible de lire l'image");
    }

    imagepng($sourceImage, $targetFile, 9);
    imagedestroy($sourceImage);
    
    // Vider le cache pour ce fichier
    clearstatcache(true, $targetFile);

    $avatarPath = $username;
    // Utiliser microtime pour un timestamp plus précis
    $timestamp = round(microtime(true) * 1000);
    
    // Mettre à jour la base de données avec le timestamp
    $stmt = $conn->prepare("UPDATE users SET avatar_path = ?, avatar_timestamp = ? WHERE id = ?");
    $stmt->bind_param("sii", $avatarPath, $timestamp, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    $_SESSION['avatar'] = $username;
    $_SESSION['avatar_timestamp'] = $timestamp;
    
    echo json_encode([
        "success" => true,
        "newPath" => "../avatars/" . $fileName,
        "timestamp" => $timestamp
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors du traitement: " . $e->getMessage()]);
}
?>