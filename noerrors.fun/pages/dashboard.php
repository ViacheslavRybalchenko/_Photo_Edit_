<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare("SELECT username, email, created_at, notify_new_comment, photo_user_path FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

$photoPath = !empty($user['photo_user_path']) ? $user['photo_user_path'] : '/uploads/images/default_photo_user.png';
$username = $user['username'];

function getUserImages($username) {
    $dir = __DIR__ . '/../uploads/images/';
    $webPath = '/uploads/images/';
    $files = [];

    if (is_dir($dir)) {
        foreach (scandir($dir) as $file) {
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif'])) {
                if (strpos($file, $username . '_') === 0 || $file === 'default_photo_user.png') {
                    $files[] = $webPath . $file;
                }
            }
        }
    }
    return $files;
}

$userImages = getUserImages($username);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Кабінет користувача</title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body>
    <div class="container-user-dashboard">
        <div class="user-dashboard">
            <h2>Кабінет користувача <span id="username-dashboard-hello"><?php echo htmlspecialchars($username); ?></span></h2>
            <form id="dashboard-form" action="/actions/save_profile.php" method="post" enctype="multipart/form-data">
                <div class="content">
                    <div class="photo-section">
                        <div class="photo">
                            <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Фото користувача" id="user-photo-preview" data-default="<?php echo htmlspecialchars($photoPath); ?>">
                        </div>
                        <div class="photo-buttons">
                            <label for="photo-user-input" class="photo-upload-btn">Вибрати файл</label>
                            <input type="file" name="photo_user" accept="image/*" id="photo-user-input">
                            <div id="selected-file-name" class="file-name-display">Файл не вибрано</div>
                        </div>
                    </div>

                    <div class="info-section">
                        <div class="info-item"><strong><label>Ім’я користувача:</label></strong>
                            <span><input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></span>
                        </div>
                        <div class="info-item"><strong><label>Email:</label></strong>
                            <span><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></span>
                        </div>
                        <div class="info-item"><strong><label>Дата реєстрації:</label></strong>
                            <span><input type="text" value="<?php echo date('d.m.Y, H:i:s', strtotime($user['created_at'])); ?>" readonly></span>
                        </div>
                        <div class="info-item">
                            <strong><label for="notify_new_comment">Повідомляти про новий коментар:</label></strong>
                            <span>
                                <input type="checkbox" id="notify_new_comment" name="notify_new_comment" value="1" <?php echo ($user['notify_new_comment']) ? 'checked' : ''; ?>>
                            </span>
                        </div>
                        <input type="hidden" name="old_photo_path" value="<?php echo htmlspecialchars($user['photo_user_path']); ?>">
                        <input type="hidden" name="selected_server_photo" id="selected_server_photo" value="">
                        <input type="hidden" name="photo_user_path" id="photo_user_path" value="<?php echo htmlspecialchars($photoPath); ?>">

                        <div class="actions">
                            <a href="index.php?page=forgot_password" class="change-password">Змінити пароль</a>
                            <button type="submit" class="save">Зберегти</button>
                            <button type="reset" class="cancel">Відмінити</button>
                        </div>
                    </div>
                </div>
                <div class="image-server-selector">
                    <button type="button" id="toggle-image-panel" class="photo-server-btn">Обрати зображення з сервера</button>
                    <div id="server-image-panel" class="hidden">
                        <?php foreach ($userImages as $img): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" data-path="<?php echo htmlspecialchars($img); ?>" alt="" class="selectable-server-image">
                        <?php endforeach; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="/assets/js/dashboard.js"></script>
</body>
</html>
