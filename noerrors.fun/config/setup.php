<?php
require_once 'database.php';

try {
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Таблиця користувачів
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        confirmed BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

    // Таблиця для фото
    $pdo->exec("CREATE TABLE IF NOT EXISTS photos (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        likes INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE)");

    echo "База даних успішно створена!";
} catch (PDOException $e) {
    die("Помилка створення БД: " . $e->getMessage());
}
?>
