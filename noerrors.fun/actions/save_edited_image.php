<?php
/*
// Увімкнути помилки для дебагу
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

// Перевірка сесії
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Користувач не авторизований']);
    exit;
}

$userId = $_SESSION['user_id'];

// Отримання JSON-запиту
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Перевірка вхідних даних
if (
    !$data ||
    !isset($data['image']) ||
    !preg_match('/^data:image\/jpeg;base64,/', $data['image']) ||
    !isset($data['image_id'])
) {
    echo json_encode(['success' => false, 'error' => 'Невірні вхідні дані']);
    exit;
}

$imageId = (int)$data['image_id'];
$base64Image = str_replace('data:image/jpeg;base64,', '', $data['image']);
$imageData = base64_decode($base64Image);
if ($imageData === false) {
    echo json_encode(['success' => false, 'error' => 'Помилка декодування зображення']);
    exit;
}

// Підключення до БД
require_once __DIR__ . '/../config/database.php';

// Отримання імені користувача
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Користувача не знайдено']);
    exit;
}
$username = preg_replace('/[^a-zA-Z0-9_-]/', '', $user['username']); // для безпеки

// Перевірка, що зображення належить користувачу
$stmt = $pdo->prepare("SELECT image_path FROM images WHERE id = ? AND user_id = ?");
$stmt->execute([$imageId, $userId]);
$original = $stmt->fetch();

if (!$original) {
    echo json_encode(['success' => false, 'error' => 'Оригінальне зображення не знайдено']);
    exit;
}

// Параметри нового файлу
$ext = 'jpg';
$timestamp = round(microtime(true) * 1000); // мілісекунди
$newFileName = $username . '_' . $timestamp . '_edited.' . $ext;
$uploadsDir = __DIR__ . '/../uploads/images/';
$webPath = '/uploads/images/';
$fullPath = $uploadsDir . $newFileName;
$relativePath = $webPath . $newFileName;

// Спроба зберегти файл
if (file_put_contents($fullPath, $imageData) === false) {
    echo json_encode(['success' => false, 'error' => 'Не вдалося зберегти зображення']);
    exit;
}

// Додавання запису в базу
$stmt = $pdo->prepare("INSERT INTO images (user_id, image_path, created_at) VALUES (?, ?, NOW())");
$success = $stmt->execute([$userId, $relativePath]);

if ($success) {
    echo json_encode(['success' => true, 'path' => $relativePath]);
} else {
    echo json_encode(['success' => false, 'error' => 'Не вдалося записати в базу']);
}
