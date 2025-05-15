<?php

// Підключення до бази даних
function connectDB() {
    require_once __DIR__ . 'http://localhost/photo_editing_app/config/database.php';
    try {
        return new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    } catch (PDOException $e) {
        die("Помилка підключення: " . $e->getMessage());
    }
}

// Функція для перевірки, чи користувач авторизований
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Функція для редиректу
function redirect($url) {
    header("Location: $url");
    exit();
}

// Функція для очищення даних від шкідливого коду
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

?>
