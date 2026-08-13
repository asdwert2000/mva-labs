<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

require_login();
$current = 'editor';

// ===== ОПИСАНИЕ РЕДАКТИРУЕМЫХ БЛОКОВ =====
$blocks = [
    'hero_badge'    => ['label' => 'Бейдж в hero', 'type' => 'text'],
    'hero_title'    => ['label' => 'Заголовок hero', 'type' => 'text'],
    'hero_highlight'=> ['label' => 'Выделенная часть заголовка', 'type' => 'text'],
    'hero_desc'     => ['label' => 'Описание в hero', 'type' => 'textarea'],
    'utp_title'     => ['label' => 'Заголовок «Что мы умножаем»', 'type' => 'text'],
    'utp_subtitle'  => ['label' => 'Подзаголовок УТП', 'type' => 'textarea'],
    'portfolio_title' => ['label' => 'Заголовок портфолио', 'type' => 'text'],
    'portfolio_subtitle' => ['label' => 'Подзаголовок портфолио', 'type' => 'textarea'],
    'prices_title'  => ['label' => 'Заголовок цен', 'type' => 'text'],
    'prices_subtitle' => ['label' => 'Подзаголовок цен', 'type' => 'textarea'],
    'form_title'    => ['label' => 'Заголовок формы', 'type' => 'text'],
    'footer_slogan' => ['label' => 'Слоган в подвале', 'type' => 'text'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($blocks as $key => $cfg) {
        $value = $_POST[$key] ?? '';
        if ($cfg['type'] === 'textarea') {
            $value = trim($value);
        } else {
            $value = trim(strip_tags($value));
        }
        set_content($key, $value);
    }
    flash('Контент сохранён. Он будет подставлен на сайт после обновления страницы.');
    redirect('editor.php');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактор контента — MVA Labs Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <h1>Редактор контента</h1>
        </header>

        <?= flash_alert() ?>

        <form method="POST" action="editor.php" class="card">
            <?= csrf_field() ?>

            <?php foreach ($blocks as $key => $cfg): ?>
                <label class="field">
                    <span class="field__label"><?= e($cfg['label']) ?></span>
                    <?php if ($cfg['type'] === 'textarea'): ?>
                        <textarea name="<?= e($key) ?>" rows="3"><?= e(get_content($key)) ?></textarea>
                    <?php else: ?>
                        <input type="text" name="<?= e($key) ?>" value="<?= e(get_content($key)) ?>">
                    <?php endif; ?>
                </label>
            <?php endforeach; ?>

            <button type="submit" class="btn btn--primary">Сохранить</button>
        </form>

        <p class="muted" style="margin-top: 12px;">
            ⚠️ Тексты сохраняются в базу данных. Для отображения на самом сайте эти блоки
            должны читаться из БД (см. конвертацию index.html → index.php).
        </p>
    </main>
</body>
</html>