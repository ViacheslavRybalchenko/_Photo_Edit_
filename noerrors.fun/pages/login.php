<h2>Увійти</h2>
<?php
if (!empty($_SESSION['message'])) {
    echo '<div class="alert" style="color: green; padding: 10px; text-align: center;">' . 
        htmlspecialchars($_SESSION['message']) . 
        '</div>';
    unset($_SESSION['message']);
}
?>
<form action="actions.php" method="post">
    <input type="hidden" name="action" value="login">

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" placeholder="Email" required>

    <label for="password">Пароль:</label>
    <div class="password-container">
        <input type="password" name="password" id="password" placeholder="Пароль" required>
        <span class="toggle-password" onclick="togglePassword('password')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5C7 5 3.1 8.3 1.5 12C3.1 15.7 7 19 12 19C17 19 20.9 15.7 22.5 12C20.9 8.3 17 5 12 5Z" 
                      stroke="#007BFF" stroke-width="2" fill="none"/>
                <circle cx="12" cy="12" r="3" stroke="#007BFF" stroke-width="2" fill="none"/>
            </svg>
        </span>
    </div>

    <button type="submit">Увійти</button>
</form>

<a href="index.php?page=register">Реєстрація</a> |
<a href="index.php?page=forgot_password">Забули пароль?</a>

<?php if (isset($_SESSION['message'])): ?>
    <p style="color:red;"><?= $_SESSION['message']; unset($_SESSION['message']); ?></p>
<?php endif; ?>

<!-- Підключення зовнішнього JS -->
<script src="/assets/js/login.js"></script>
