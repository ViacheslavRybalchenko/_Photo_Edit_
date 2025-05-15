<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

// Перевірка авторизації
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизовано']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$image_id = $data['image_id'] ?? null;
$comment = trim($data['comment'] ?? '');

if (!$image_id || !$comment) {
    echo json_encode(['success' => false, 'error' => 'Невірні дані']);
    exit;
}

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);

// Додавання коментаря
$stmt = $pdo->prepare("INSERT INTO comments (image_id, user_id, comment, created_at) VALUES (:image_id, :user_id, :comment, NOW())");
$stmt->execute([
    ':image_id' => $image_id,
    ':user_id' => $_SESSION['user_id'],
    ':comment' => $comment
]);

// Отримуємо username коментатора
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $_SESSION['user_id']]);
$commenter_username = $stmt->fetchColumn();

// Отримуємо дані про власника зображення
$stmt = $pdo->prepare("
    SELECT u.id AS owner_id, u.email, u.username AS owner_username, u.notify_new_comment
    FROM images i
    JOIN users u ON i.user_id = u.id
    WHERE i.id = :image_id
    LIMIT 1
");
$stmt->execute([':image_id' => $image_id]);
$image_owner = $stmt->fetch(PDO::FETCH_ASSOC);

if ($image_owner) {
    $owner_id = (int)$image_owner['owner_id'];
    $owner_email = $image_owner['email'];
    $owner_username = $image_owner['owner_username'];
    $notify = (int)$image_owner['notify_new_comment'];
    $commenter_id = (int)$_SESSION['user_id'];

    // Не надсилати, якщо власник = коментатор або notify = 0
    if ($owner_id !== $commenter_id && $notify === 1) {
        $comment_time = date('Y-m-d H:i:s');
        $image_link = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/index.php?page=image_info&id=" . $image_id;

        $subject = "Новий коментар до вашого зображення";
        $message = "Привіт, $owner_username!\n\n"
                 . "Користувач \"$commenter_username\" залишив новий коментар до вашого зображення.\n\n"
                 . "Дата та час: $comment_time\n"
                 . "Коментар:\n\"$comment\"\n\n"
                 . "Переглянути зображення: $image_link\n\n"
                 . "З повагою,\nКоманда сайту Фоторедактор";

        $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n" .
                   "Content-Type: text/plain; charset=UTF-8\r\n";

        // Надсилання листа
        $mail_result = mail(
            $owner_email,
            "=?UTF-8?B?" . base64_encode($subject) . "?=",
            $message,
            $headers
        );


    }
}

// Відповідь
echo json_encode([
    'success' => true,
    'username' => $commenter_username,
    'comment' => $comment,
    'created_at' => date('Y-m-d H:i:s')
], JSON_UNESCAPED_UNICODE);
