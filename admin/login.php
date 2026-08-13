<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (attempt_login($login, $password)) {
        redirect('index.php');
    }
    $error = 'Неверный логин или пароль';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — MVA Labs Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="login-box">
        <div class="login-logo">MVA <span>Labs</span></div>
        <h1>Вход в админку</h1>

        <?php if ($error): ?>
            <div class="alert alert--error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">
            <?= csrf_field() ?>
            <label>
                Логин
                <input type="text" name="login" required autofocus autocomplete="username">
            </label>
            <label>
                Пароль
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button type="submit" class="btn btn--primary btn--full">Войти</button>
        </form>
    </div>
</body>
</html>