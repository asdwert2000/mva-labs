<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

require_login();
$current = 'settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'contacts') {
        set_setting('contact_email', trim($_POST['contact_email'] ?? ''));
        set_setting('contact_phone', trim($_POST['contact_phone'] ?? ''));
        set_setting('telegram_link', trim($_POST['telegram_link'] ?? ''));
        flash('Контакты сохранены.');
    }

    if ($action === 'password') {
        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPass, get_setting('admin_password'))) {
            flash('Текущий пароль неверен.', 'error');
        } elseif (strlen($newPass) < 6) {
            flash('Новый пароль должен быть не короче 6 символов.', 'error');
        } elseif ($newPass !== $confirmPass) {
            flash('Пароли не совпадают.', 'error');
        } else {
            set_setting('admin_password', password_hash($newPass, PASSWORD_DEFAULT));
            flash('Пароль изменён.');
        }
    }

    redirect('settings.php');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки — MVA Labs Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <h1>Настройки</h1>
        </header>

        <?= flash_alert() ?>

        <form method="POST" action="settings.php" class="card">
            <h2>Контакты и соцсети</h2>
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="contacts">

            <label class="field">
                <span class="field__label">Email</span>
                <input type="email" name="contact_email" value="<?= e(get_setting('contact_email')) ?>">
            </label>
            <label class="field">
                <span class="field__label">Телефон</span>
                <input type="text" name="contact_phone" value="<?= e(get_setting('contact_phone')) ?>">
            </label>
            <label class="field">
                <span class="field__label">Ссылка на Telegram</span>
                <input type="text" name="telegram_link" value="<?= e(get_setting('telegram_link')) ?>">
            </label>
            <button type="submit" class="btn btn--primary">Сохранить контакты</button>
        </form>

        <form method="POST" action="settings.php" class="card">
            <h2>Смена пароля</h2>
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="password">

            <label class="field">
                <span class="field__label">Текущий пароль</span>
                <input type="password" name="current_password" required autocomplete="current-password">
            </label>
            <label class="field">
                <span class="field__label">Новый пароль</span>
                <input type="password" name="new_password" required autocomplete="new-password" minlength="6">
            </label>
            <label class="field">
                <span class="field__label">Повторите новый пароль</span>
                <input type="password" name="confirm_password" required autocomplete="new-password">
            </label>
            <button type="submit" class="btn btn--primary">Сменить пароль</button>
        </form>
    </main>
</body>
</html>