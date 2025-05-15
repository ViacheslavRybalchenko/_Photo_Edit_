<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'], $_POST['image_id'], $_POST['like_value'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Недостатньо даних']);
    exit;
}

$user_id = $_SESSION['user_id'];
$image_id = (int)$_POST['image_id'];
$like_value = (int)$_POST['like_value'];

if (!in_array($like_value, [1, -1])) {
    http_response_code(400);
    echo json_encode(['error' => 'Некоректне значення']);
    exit;
}

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Перевірка, чи вже є лайк/дизлайк від користувача
$stmt = $pdo->prepare("SELECT * FROM likes WHERE image_id = ? AND user_id = ?");
$stmt->execute([$image_id, $user_id]);
$existing = $stmt->fetch();

if ($existing) {
    http_response_code(409); // Код помилки HTTP 409 Conflict 
    echo json_encode(['error' => 'Вже голосував']);
    exit;
}

// Додаємо запис
$stmt = $pdo->prepare("INSERT INTO likes (image_id, user_id, like_value) VALUES (?, ?, ?)");
$stmt->execute([$image_id, $user_id, $like_value]);

// Повертаємо нові значення
$stmt = $pdo->prepare("SELECT 
    SUM(CASE WHEN like_value = 1 THEN 1 ELSE 0 END) AS like_count,
    SUM(CASE WHEN like_value = -1 THEN 1 ELSE 0 END) AS dislike_count
    FROM likes WHERE image_id = ?");
$stmt->execute([$image_id]);
$counts = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'like_count' => (int) $counts['like_count'],
    'dislike_count' => (int) $counts['dislike_count']
]);
