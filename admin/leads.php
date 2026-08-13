<?php
require_once 'config.php';
require_once 'includes/auth.php';
requireLogin();

// Обновление статуса
if ($_GET['action'] === 'status' && $_GET['id']) {
    $status = $_GET['status'] ?? 'processed';
    $stmt = $pdo->prepare("UPDATE leads SET status = ? WHERE id = ?");
    $stmt->execute([$status, $_GET['id']]);
    header('Location: /admin/leads.php?updated=1');
    exit;
}

// Удаление
if ($_GET['action'] === 'delete' && $_GET['id']) {
    $stmt = $pdo->prepare("DELETE FROM leads WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header('Location: /admin/leads.php?deleted=1');
    exit;
}

$leads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки — MVA Labs Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar__logo">
                <span class="logo__multiply">×</span>
                <span class="logo__text">MVA <span class="logo__labs">Labs</span></span>
            </div>
            <nav class="sidebar__nav">
                <a href="/admin/index.php">📊 Дашборд</a>
                <a href="/admin/editor.php?page=hero">✏️ Редактор страниц</a>
                <a href="/admin/portfolio.php">🖼️ Портфолио</a>
                <a href="/admin/leads.php" class="active">📩 Заявки</a>
                <a href="/admin/settings.php">⚙️ Настройки</a>
                <a href="/admin/logout.php" style="margin-top: 40px; color: #F87171;">🚪 Выход</a>
            </nav>
            <div class="sidebar__user">
                👤 <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
            </div>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>📩 Заявки</h1>
                <span style="color: #9CA3AF;">Всего: <?= count($leads) ?></span>
            </div>
            
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">✅ Статус обновлён!</div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">🗑️ Заявка удалена!</div>
            <?php endif; ?>
            
            <div class="table-wrapper">
                <table class="leads-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Имя</th>
                            <th>Телефон</th>
                            <th>Email</th>
                            <th>Пакет</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leads)): ?>
                            <tr><td colspan="8" style="text-align: center; color: #6B7280;">Пока нет заявок</td></tr>
                        <?php else: ?>
                            <?php foreach ($leads as $lead): ?>
                                <tr>
                                    <td>#<?= $lead['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($lead['name']) ?></strong></td>
                                    <td><a href="tel:<?= htmlspecialchars($lead['phone']) ?>"><?= htmlspecialchars($lead['phone']) ?></a></td>
                                    <td><?= htmlspecialchars($lead['email'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($lead['package'] ?? '-') ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $lead['status'] ?>">
                                            <?= ['new' => '🆕 Новое', 'processed' => '📞 В обработке', 'closed' => '✅ Закрыто'][$lead['status']] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($lead['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($lead['status'] === 'new'): ?>
                                                <a href="?action=status&id=<?= $lead['id'] ?>&status=processed" class="btn-small btn-process">Взять</a>
                                            <?php endif; ?>
                                            <?php if ($lead['status'] === 'processed'): ?>
                                                <a href="?action=status&id=<?= $lead['id'] ?>&status=closed" class="btn-small btn-close">Закрыть</a>
                                            <?php endif; ?>
                                            <a href="?action=delete&id=<?= $lead['id'] ?>" class="btn-small btn-delete" onclick="return confirm('Удалить заявку?')">🗑️</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>