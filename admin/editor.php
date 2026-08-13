<?php
require_once 'config.php';
require_once 'includes/auth.php';
requireLogin();

$pageKey = $_GET['page'] ?? 'hero';
$page = getPageContent($pdo, $pageKey);

if (!$page) {
    die('Страница не найдена');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'page_key' => $pageKey,
        'title' => $_POST['title'] ?? '',
        'content' => $_POST['content'] ?? '',
        'meta_title' => $_POST['meta_title'] ?? '',
        'meta_description' => $_POST['meta_description'] ?? '',
        'meta_keywords' => $_POST['meta_keywords'] ?? ''
    ];
    
    if (updatePageContent($pdo, $pageKey, $data)) {
        $success = '✅ Контент успешно обновлён!';
        $page = getPageContent($pdo, $pageKey); // Перезагружаем данные
    } else {
        $error = '❌ Ошибка при сохранении';
    }
}

// Доступные страницы для редактора
$pages = [
    'hero' => 'Главный экран (Hero)',
    'utp' => 'Блок "Что мы умножаем"',
    'portfolio' => 'Портфолио (описание)',
    'prices' => 'Цены и пакеты',
    'footer_seo' => 'SEO-текст в подвале'
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактор — MVA Labs Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- ===== САЙДБАР ===== -->
        <aside class="sidebar">
            <div class="sidebar__logo">
                <span class="logo__multiply">×</span>
                <span class="logo__text">MVA <span class="logo__labs">Labs</span></span>
            </div>
            <nav class="sidebar__nav">
                <a href="/admin/index.php">📊 Дашборд</a>
                <a href="/admin/editor.php?page=hero" class="active">✏️ Редактор страниц</a>
                <a href="/admin/portfolio.php">🖼️ Портфолио</a>
                <a href="/admin/leads.php">📩 Заявки</a>
                <a href="/admin/settings.php">⚙️ Настройки</a>
                <a href="/admin/logout.php" style="margin-top: 40px; color: #F87171;">🚪 Выход</a>
            </nav>
            <div class="sidebar__user">
                👤 <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
            </div>
        </aside>
        
        <!-- ===== ОСНОВНОЙ КОНТЕНТ ===== -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>✏️ Редактор контента</h1>
                <a href="/" target="_blank" class="btn-view-site">🌐 Смотреть сайт</a>
            </div>
            
            <!-- ===== ВЫБОР СТРАНИЦЫ ===== -->
            <div class="page-selector">
                <?php foreach ($pages as $key => $label): ?>
                    <a href="/admin/editor.php?page=<?= $key ?>" 
                       class="page-selector__item <?= $key === $pageKey ? 'active' : '' ?>">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- ===== СООБЩЕНИЯ ===== -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <!-- ===== ФОРМА РЕДАКТОРА ===== -->
            <form method="POST" class="editor-form">
                <div class="form-group">
                    <label for="title">Заголовок блока (H2)</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($page['title'] ?? '') ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="content">Содержимое блока (HTML)</label>
                    <textarea id="content" name="content" rows="20" class="form-control editor-textarea"><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
                    <p class="form-hint">Можно использовать HTML-теги: p, strong, h3, ul, li, div, span, a</p>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="meta_title">Meta Title (заголовок в поиске)</label>
                        <input type="text" id="meta_title" name="meta_title" value="<?= htmlspecialchars($page['meta_title'] ?? '') ?>" class="form-control" maxlength="70">
                        <p class="form-hint">70 символов максимум</p>
                    </div>
                    <div class="form-group">
                        <label for="meta_description">Meta Description (описание в поиске)</label>
                        <textarea id="meta_description" name="meta_description" rows="2" class="form-control" maxlength="160"><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
                        <p class="form-hint">160 символов максимум</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="meta_keywords">Meta Keywords (ключевые слова)</label>
                    <input type="text" id="meta_keywords" name="meta_keywords" value="<?= htmlspecialchars($page['meta_keywords'] ?? '') ?>" class="form-control" placeholder="через запятую">
                </div>
                
                <button type="submit" class="btn-save">💾 Сохранить изменения</button>
            </form>
            
            <!-- ===== ПРЕДПРОСМОТР ===== -->
            <div class="preview-section">
                <h3>👁️ Предпросмотр содержимого</h3>
                <div class="preview-box">
                    <?= $page['content'] ?? '' ?>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Автоматическое сохранение Ctrl+S
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.querySelector('.editor-form').submit();
            }
        });
    </script>
</body>
</html>