<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

require_login();

$leadCount = (int)db()->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$unreadCount = (int)db()->query("SELECT COUNT(*) FROM leads WHERE readed = 0")->fetchColumn();
$portfolioCount = (int)db()->query("SELECT COUNT(*) FROM portfolio")->fetchColumn();

$recentLeads = db()->query("SELECT * FROM leads ORDER BY id DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд — MVA Labs Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <h1>Дашборд</h1>
            <a href="logout.php" class="btn btn--ghost">Выйти</a>
        </header>

        <?= flash_alert() ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-card__value"><?= $leadCount ?></div>
                <div class="stat-card__label">Заявок всего</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value"><?= $unreadCount ?></div>
                <div class="stat-card__label">Новых</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value"><?= $portfolioCount ?></div>
                <div class="stat-card__label">Работ в портфолио</div>
            </div>
        </div>

        <section class="card">
            <h2>Последние заявки</h2>
            <?php if (!$recentLeads): ?>
                <p class="muted">Заявок пока нет. Они появятся здесь после отправки формы на сайте.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Имя</th>
                            <th>Телефон</th>
                            <th>Пакет</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLeads as $lead): ?>
                            <tr>
                                <td><?= e($lead['created_at']) ?></td>
                                <td><?= e($lead['name']) ?></td>
                                <td><?= e($lead['phone']) ?></td>
                                <td><?= e($lead['package']) ?></td>
                                <td>
                                    <span class="badge <?= $lead['readed'] ? 'badge--done' : 'badge--new' ?>">
                                        <?= $lead['readed'] ? 'Прочитана' : 'Новая' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>