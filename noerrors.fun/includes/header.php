<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фоторедактор</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php
require_once 'config/check_db.php';

$editorLink = "index.php?page=editor";

if (isset($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT photo_user_path FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $photoPath = $user['photo_user_path'];

        $stmt2 = $pdo->prepare("SELECT id FROM images WHERE image_path = ?");
        $stmt2->execute([$photoPath]);
        $image = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $editorLink = "index.php?page=editor&id=" . (int)$image['id'];
        } else {
            $defaultPhotoPath = '/uploads/images/default_photo_user.png';
            $stmt3 = $pdo->prepare("SELECT id FROM images WHERE image_path = ?");
            $stmt3->execute([$defaultPhotoPath]);
            $defaultImage = $stmt3->fetch(PDO::FETCH_ASSOC);

            if ($defaultImage) {
                $editorLink = "index.php?page=editor&id=" . (int)$defaultImage['id'];
            }
        }
    }
}
?>

<header>
    <nav>
        <div class="burger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <ul id="menu">
            <?php if ($db_exists): ?>
                <li><a href="index.php">Головна</a></li>
                <li><a href="index.php?page=gallery">Галерея</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?= htmlspecialchars($editorLink) ?>">Редактор</a></li>
                    <li><a href="index.php?page=dashboard">Кабінет</a></li>
                    <li><a href="/actions/logout.php">Вийти</a></li>
                <?php else: ?>
                    <li><a href="index.php?page=login">Увійти</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="setup.php">Встановити БД</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Підключення js скрипта -->
<script src="/assets/js/header.js"></script>
