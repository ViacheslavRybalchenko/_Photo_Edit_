<?php
require_once 'database.php';

// Перевірка, чи підключення до БД встановлено
if (!$pdo) {
    $db_exists = false;
} else {
    // Перевіряємо, чи є необхідні таблиці
    try {
        $result = $pdo->query("SHOW TABLES LIKE 'users'");
        $db_exists = $result->rowCount() > 0;
    } catch (PDOException $e) {
        $db_exists = false;
    }
}
?>
