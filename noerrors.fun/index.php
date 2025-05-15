<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

$page = $_GET['page'] ?? 'home';

?>

<main id="main-content">
    <?php include "pages/$page.php"; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
