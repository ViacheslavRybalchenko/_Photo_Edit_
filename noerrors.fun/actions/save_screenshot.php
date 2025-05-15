<?php
session_start();
//file_put_contents(__DIR__ . '/debug_session.txt', print_r($_SESSION, true));
require_once __DIR__ . '/../config/database.php';

/*if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Користувач не авторизований']);
    exit;
}*/

$data = json_decode(file_get_contents('php://input'), true);


if (!isset($data['image'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Немає зображення']);
    exit;
}

$imageData = $data['image'];
$imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
$imageData = str_replace(' ', '+', $imageData);
$decodedImage = base64_decode($imageData);
//file_put_contents(__DIR__ . '/debug_image_data.txt', print_r($imageData, true), FILE_APPEND);
if (!$decodedImage) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Невірне зображення']);
    exit;
}

// Маска: screenshot_<username>_<мілісекунди>.jpg
$username = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_SESSION['username']);
$current_time = round(microtime(true) * 1000);
$filename = "screenshot_{$username}_{$current_time}.jpg";
$savePath = "/uploads/images/" . $filename;
$absolutePath = $_SERVER['DOCUMENT_ROOT'] . $savePath;


// Збереження файлу
if (!file_exists(dirname($absolutePath))) {
    mkdir(dirname($absolutePath), 0755, true);
}
/* Дебаг */
file_put_contents(__DIR__ . '/debug_log.txt', "__DIR__: " . __DIR__ . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/debug_log.txt', "Абсолютний шлях: $absolutePath\n", FILE_APPEND);
file_put_contents(__DIR__ . '/debug_log.txt', "Чи існує директорія? " . (is_dir(dirname($absolutePath)) ? 'Так' : 'Ні') . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/debug_log.txt', "Права доступу до директорії: " . decoct(fileperms(dirname($absolutePath)) & 0777) . "\n", FILE_APPEND);

$result = file_put_contents($absolutePath, $decodedImage);
file_put_contents(__DIR__ . '/debug_log.txt', "Результат запису: $result\n", FILE_APPEND);

if ($result === false) {
    file_put_contents(__DIR__ . '/debug_log.txt', "ПОМИЛКА ЗАПИСУ В ФАЙЛ!\n", FILE_APPEND);
}


// Запис в БД
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare("INSERT INTO images (user_id, image_path, created_at) VALUES (:user_id, :image_path, NOW())");
$stmt->execute([
    ':user_id' => $_SESSION['user_id'],
    ':image_path' => $savePath
]);

echo json_encode(['success' => true]);
