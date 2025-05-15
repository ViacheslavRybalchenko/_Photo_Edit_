<?php
require_once 'config/database.php';

try {
    // Якщо немає підключення до БД, створюємо її
    if (!$pdo) {
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
    }

    // Створення таблиці користувачів
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        verified BOOLEAN DEFAULT 0,
        activation_code VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        reset_token VARCHAR(64) DEFAULT NULL,
        reset_expires DATETIME DEFAULT NULL,
        photo_user_path VARCHAR(255) DEFAULT '/uploads/images/default_photo_user.png', 
        notify_new_comment BOOLEAN DEFAULT 1
    )");

    // Таблиця для збереження зображень
    $pdo->exec("CREATE TABLE IF NOT EXISTS images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Таблиця історії редагувань
    $pdo->exec("CREATE TABLE IF NOT EXISTS image_edits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_id INT NOT NULL,
        edit_details TEXT NOT NULL,
        edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
    )");

    // Таблиця коментарів
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_id INT NOT NULL,
        user_id INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Таблиця лайків
    $pdo->exec("CREATE TABLE IF NOT EXISTS likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_id INT NOT NULL,
        user_id INT NOT NULL,
        like_value INT NOT NULL, 
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Якщо все успішно, перенаправляємо на головну сторінку
    header("Location: index.php");
    exit;
} catch (PDOException $e) {
    die("Помилка встановлення БД: " . $e->getMessage());
}
?>
