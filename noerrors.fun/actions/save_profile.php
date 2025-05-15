<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php?page=login');
    exit;
}

$user_id = $_SESSION['user_id'];

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$notify = isset($_POST['notify_new_comment']) ? 1 : 0;
$old_photo = $_POST['old_photo_path'] ?? '';
$server_selected = $_POST['selected_server_photo'] ?? '';
$new_photo_path = $old_photo;

// Якщо завантажено новий файл
if (isset($_FILES['photo_user']) && $_FILES['photo_user']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['photo_user']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['photo_user']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($ext, $allowed)) {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();
        $username_db = $row ? $row['username'] : 'user';

        $filename = $username_db . '_' . round(microtime(true) * 1000) . '.' . $ext;
        $upload_dir = __DIR__ . '/../uploads/images/';
        $destination = $upload_dir . $filename;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0775, true);
        }

        if (move_uploaded_file($tmp, $destination)) {
            $new_photo_path = '/uploads/images/' . $filename;

            // Перевірка дубля
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM images WHERE user_id = ? AND image_path = ?");
            $checkStmt->execute([$user_id, $new_photo_path]);
            $exists = $checkStmt->fetchColumn();

            if (!$exists) {
                $insertStmt = $pdo->prepare("INSERT INTO images (user_id, image_path) VALUES (?, ?)");
                $insertStmt->execute([$user_id, $new_photo_path]);
            }
        }
    }
} elseif (!empty($server_selected)) {
    $new_photo_path = $server_selected;
}

// Оновлення в БД
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, notify_new_comment = ?, photo_user_path = ? WHERE id = ?");
$stmt->execute([$username, $email, $notify, $new_photo_path, $user_id]);

header('Location: ../index.php?page=dashboard');
exit;
