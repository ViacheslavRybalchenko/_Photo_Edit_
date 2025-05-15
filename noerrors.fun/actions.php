<?php
session_start();
require 'config/database.php'; // Підключення до бази даних

// Ініціалізація PDO
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Головна зміна — підтримка GET-параметру 'action'
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
    
        if ($password !== $confirm_password) {
            $_SESSION['message'] = 'Пароль і повторне введення паролю не співпадають';
            header("Location: index.php?page=register");
            exit;
        }
    
        if (strlen($password) < 8) {
            $_SESSION['message'] = 'Мінімальна довжина паролю 8 символів';
            header("Location: index.php?page=register");
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Цей email вже використовується!';
            header("Location: index.php?page=register");
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $activation_code = md5(uniqid(rand(), true));
        $default_photo_user_path = '/uploads/images/default_photo_user.png';

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, activation_code, photo_user_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password, $activation_code, $default_photo_user_path]);

        $verification_link = "https://noerrors.fun/actions.php?action=verify&code=$activation_code";
        mail($email, "Підтвердження реєстрації", "Перейдіть за посиланням для активації: $verification_link");

        $_SESSION['message'] = 'Реєстрація успішна! Перевірте email для активації.';
        header("Location: index.php?page=login");
        exit;

    case 'login':
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT id, password, verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['verified'] == 0) {
                $_SESSION['message'] = 'Підтвердіть email перед входом.';
                header("Location: index.php?page=login");
                exit;
            }

            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php?page=dashboard");
            exit;
        }

        $_SESSION['message'] = 'Невірний email або пароль';
        header("Location: index.php?page=login");
        exit;

        case 'verify':
            // Обробка GET['code']
            if (!empty($_GET['code'])) {
                $activation_code = $_GET['code'];
    
                $stmt = $pdo->prepare("SELECT id FROM users WHERE activation_code = :code AND verified = 0");
                $stmt->execute(['code' => $activation_code]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($user) {
                    $updateStmt = $pdo->prepare("UPDATE users SET verified = 1, activation_code = NULL WHERE id = :id");
                    $updateStmt->execute(['id' => $user['id']]);
    
                    $_SESSION['message'] = '✅ Верифікація успішна! Тепер ви можете увійти.';
                } else {
                    $_SESSION['message'] = '❌ Невірний або вже активований код.';
                }
            } else {
                $_SESSION['message'] = '❌ Відсутній код активації.';
            }
    
            header("Location: index.php?page=login");
            exit;
    

    case 'forgot_password':
        $email = trim($_POST['email']);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $reset_code = md5(uniqid(rand(), true));
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
            $stmt->execute([$reset_code, $email]);

            $reset_link = "http://noerrors.fun/index.php?page=reset_password&token=$reset_code";
            mail($email, "Скидання паролю", "Перейдіть за посиланням для скидання паролю: $reset_link");
        }

        $_SESSION['message'] = 'Якщо email існує в системі, на нього буде надіслано інструкції.';
        header("Location: index.php?page=login");
        exit;

    case 'reset_password':
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $_SESSION['message'] = 'Паролі не співпадають.';
            header("Location: index.php?page=reset_password&token=$token");
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['message'] = 'Мінімальна довжина паролю 8 символів';
            header("Location: index.php?page=reset_password&token=$token");
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->execute([$hashed_password, $user['id']]);

            $_SESSION['message'] = 'Пароль успішно змінено!';
            header("Location: index.php?page=login");
            exit;
        } else {
            $_SESSION['message'] = 'Невірний або прострочений токен.';
            header("Location: index.php?page=reset_password&token=$token");
            exit;
        }

    case 'logout':
        session_destroy();
        header("Location: index.php?page=login");
        exit;

    default:
        header("Location: index.php");
        exit;
}
