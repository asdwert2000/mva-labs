<?php
$current = 'editor';
include __DIR__ . '/includes/auth.php';
require_login();
?>
<aside class="sidebar">
    <div class="sidebar__logo">MVA <span>Labs</span> Admin</div>
    <nav class="sidebar__nav">
        <a href="index.php" class="<?= $current === 'index' ? 'active' : '' ?>">📊 Дашборд</a>
        <a href="editor.php" class="<?= $current === 'editor' ? 'active' : '' ?>">✏️ Редактор контента</a>
        <a href="portfolio.php" class="<?= $current === 'portfolio' ? 'active' : '' ?>">🖼️ Портфолио</a>
        <a href="settings.php" class="<?= $current === 'settings' ? 'active' : '' ?>">⚙️ Настройки</a>
    </nav>
    <div class="sidebar__footer">
        <a href="<?= SITE_URL ?>" target="_blank" rel="noopener">🌐 Открыть сайт</a>
        <a href="logout.php">🚪 Выйти</a>
    </div>
</aside>