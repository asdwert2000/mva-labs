<?php
// ============================================
// MVA Labs — ГЛАВНАЯ СТРАНИЦА
// Динамический лендинг с подключением к БД
// ============================================

// Подключаем конфигурацию админ-панели
require_once __DIR__ . '/admin/config.php';

// ===== ПОЛУЧАЕМ ВЕСЬ КОНТЕНТ ИЗ БАЗЫ ДАННЫХ =====
$hero = getPageContent($pdo, 'hero');
$utp = getPageContent($pdo, 'utp');
$portfolioText = getPageContent($pdo, 'portfolio');
$pricesText = getPageContent($pdo, 'prices');
$footerSeo = getPageContent($pdo, 'footer_seo');
$settings = getSettings($pdo);

// ===== ПОЛУЧАЕМ ПОРТФОЛИО =====
$portfolioItems = $pdo->query("SELECT * FROM portfolio WHERE active = 1 ORDER BY category, sort_order")->fetchAll();

// Группируем портфолио по категориям
$portfolioByCategory = [
    'sites' => [],
    'apps' => [],
    'strategies' => []
];

foreach ($portfolioItems as $item) {
    if (isset($portfolioByCategory[$item['category']])) {
        $portfolioByCategory[$item['category']][] = $item;
    }
}

// ===== НАЗВАНИЯ КАТЕГОРИЙ ДЛЯ ВЫВОДА =====
$categoryLabels = [
    'sites' => '📱 Разработка сайтов на заказ',
    'apps' => '📲 Дизайн мобильных приложений',
    'strategies' => '📊 Маркетинговые стратегии'
];

$categoryDescriptions = [
    'sites' => 'Мы создаём продающие сайты с уникальным дизайном, адаптивной вёрсткой и ИИ-анализом поведения пользователей. От лендингов до многостраничных корпоративных порталов.',
    'apps' => 'Разрабатываем UI/UX дизайн для приложений под iOS и Android. Учитываем поведенческие паттерны пользователей, чтобы увеличить удержание и конверсию.',
    'strategies' => 'Разрабатываем визуальные маркетинговые стратегии для вывода брендов на рынок, повышения узнаваемости и роста продаж. Используем ИИ-анализ конкурентов и трендов.'
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ===== ОСНОВНЫЕ SEO-МЕТА ТЕГИ ===== -->
    <title><?= htmlspecialchars($hero['meta_title'] ?? 'MVA Labs — Маркетинг и дизайн с ИИ. Умножаем ваш бизнес') ?></title>
    <meta name="description" content="<?= htmlspecialchars($hero['meta_description'] ?? 'MVA Labs: маркетинговое агентство полного цикла с использованием искусственного интеллекта. Создаём сайты, мобильные приложения и визуальные стратегии. Умножаем конверсию, креатив и скорость. Бесплатная консультация.') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($hero['meta_keywords'] ?? 'маркетинг с ии, дизайн с ии, создание сайтов на заказ, дизайн мобильных приложений, маркетинговая стратегия, нейросети в дизайне, разработка бренда, визуальная айдентика, MVA Labs') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($settings['site_url'] ?? 'https://mvalabs.ru') ?>/">
    
    <!-- ===== OPEN GRAPH (для соцсетей) ===== -->
    <meta property="og:title" content="<?= htmlspecialchars($hero['meta_title'] ?? 'MVA Labs — Маркетинг и дизайн с искусственным интеллектом') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($hero['meta_description'] ?? 'Создаём сайты, приложения и маркетинговые стратегии, умноженные на ИИ. Конверсия ×2, скорость ×10. Закажите консультацию.') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($settings['site_url'] ?? '') ?><?= htmlspecialchars($settings['og_image'] ?? '/images/og-image.jpg') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($settings['site_url'] ?? 'https://mvalabs.ru') ?>/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= htmlspecialchars($settings['site_name'] ?? 'MVA Labs') ?>">
    <meta property="og:locale" content="ru_RU">
    
    <!-- ===== TWITTER CARDS ===== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($hero['meta_title'] ?? 'MVA Labs — Маркетинг и дизайн с ИИ') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($hero['meta_description'] ?? 'Умножаем ваш бизнес через искусственный интеллект. Сайты, приложения, стратегии.') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($settings['site_url'] ?? '') ?><?= htmlspecialchars($settings['og_image'] ?? '/images/og-image.jpg') ?>">
    
    <!-- ===== ДОПОЛНИТЕЛЬНЫЕ SEO ===== -->
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="7 days">
    <meta name="language" content="Russian">
    <meta name="author" content="<?= htmlspecialchars($settings['site_name'] ?? 'MVA Labs') ?>">
    <meta name="copyright" content="<?= htmlspecialchars($settings['site_name'] ?? 'MVA Labs') ?>">
    <meta name="geo.region" content="RU">
    
    <!-- ===== ТЕХНИЧЕСКИЕ МЕТА-ТЕГИ ===== -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#0A0618">
    <link rel="icon" type="image/png" href="images/favicon.png">
    
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Микроразметка Schema.org для поисковых систем -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ProfessionalService",
        "name": "<?= htmlspecialchars($settings['site_name'] ?? 'MVA Labs') ?>",
        "description": "<?= htmlspecialchars($hero['meta_description'] ?? 'Агентство маркетинга и дизайна с использованием искусственного интеллекта.') ?>",
        "url": "<?= htmlspecialchars($settings['site_url'] ?? 'https://mvalabs.ru') ?>/",
        "telephone": "<?= htmlspecialchars($settings['phone'] ?? '+7-XXX-XXX-XX-XX') ?>",
        "email": "<?= htmlspecialchars($settings['email'] ?? 'hello@mvalabs.ru') ?>",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "RU"
        },
        "priceRange": "₽₽",
        "openingHours": "Mo-Fr 10:00-19:00"
    }
    </script>
</head>
<body>
    
    <!-- ========================================== -->
    <!-- ====== ШАПКА (HEADER) ====== -->
    <!-- ========================================== -->
    <header class="header" role="banner">
        <div class="container header__inner">
            <div class="logo" itemscope itemtype="https://schema.org/Organization">
                <span class="logo__multiply" aria-hidden="true">×</span>
                <span class="logo__text">MVA <span class="logo__labs">Labs</span></span>
                <meta itemprop="name" content="<?= htmlspecialchars($settings['site_name'] ?? 'MVA Labs') ?>">
                <meta itemprop="url" content="<?= htmlspecialchars($settings['site_url'] ?? 'https://mvalabs.ru') ?>/">
            </div>
            
            <button class="burger" aria-label="Открыть меню" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav class="nav" role="navigation" aria-label="Основное меню">
                <a href="#services">Услуги</a>
                <a href="#portfolio">Портфолио</a>
                <a href="#prices">Цены</a>
                <a href="#contacts">Контакты</a>
            </nav>
            
            <a href="#form" class="btn btn--small btn--primary">Заказать звонок</a>
        </div>
    </header>
    
    <!-- ========================================== -->
    <!-- ====== ГЛАВНЫЙ ЭКРАН (HERO) ====== -->
    <!-- ========================================== -->
    <section class="hero" id="hero">
        <div class="container hero__inner">
            <div class="hero__content">
                <div class="hero__badge">🚀 Искусственный интеллект в дизайне</div>
                
                <h1 class="hero__title">
                    <?= $hero['title'] ?? 'Маркетинг и дизайн,<br><span class="hero__highlight">умноженный на ИИ</span>' ?>
                </h1>
                
                <div class="hero__desc">
                    <?= $hero['content'] ?? '<strong>MVA Labs</strong> — маркетинговое агентство, которое использует искусственный интеллект для создания сайтов, мобильных приложений и визуальных стратегий. Мы превращаем маркетинговые данные в дизайн-решения за часы, а не недели. <strong>Умножаем вашу ценность с помощью нейросетей.</strong>' ?>
                </div>
                
                <div class="hero__buttons">
                    <a href="#form" class="btn btn--primary">Рассчитать стоимость</a>
                    <a href="#portfolio" class="btn btn--secondary">Смотреть кейсы ↓</a>
                </div>
            </div>
            
            <div class="hero__visual" aria-hidden="true">
                <div class="hero__circle"></div>
                <div class="hero__ai-icon">AI</div>
            </div>
        </div>
    </section>
    
    <!-- ========================================== -->
    <!-- ====== БЛОК УТП (ЧТО МЫ УМНОЖАЕМ) ====== -->
    <!-- ========================================== -->
    <section class="utp" id="services" aria-labelledby="utp-title">
        <div class="container">
            <h2 class="section-title" id="utp-title">
                <?= $utp['title'] ?? 'Что мы <span class="highlight">умножаем</span>?' ?>
            </h2>
            <p class="section-subtitle">
                MVA Labs — агентство маркетинга и дизайна с искусственным интеллектом. 
                Мы делаем ваш бизнес эффективнее в 10 раз.
            </p>
            
            <div class="utp__grid">
                <?= $utp['content'] ?? '
                <div class="utp__item">
                    <div class="utp__icon" aria-hidden="true">🔄</div>
                    <h3>Креатив</h3>
                    <p><strong>Генерируем сотни вариантов дизайна</strong> по одному брифу. Искусственный интеллект масштабирует идеи без потери качества. Вы получаете максимум визуальных решений для A/B-тестирования.</p>
                </div>
                <div class="utp__item">
                    <div class="utp__icon" aria-hidden="true">📈</div>
                    <h3>Конверсия</h3>
                    <p><strong>Каждый элемент дизайна просчитан нейросетью</strong> на основе данных о поведении вашей целевой аудитории. Мы создаём не просто красиво, а эффективно — с прогнозируемым ROI.</p>
                </div>
                <div class="utp__item">
                    <div class="utp__icon" aria-hidden="true">⚡</div>
                    <h3>Скорость</h3>
                    <p><strong>Запускаем A/B-тестирование нескольких гипотез</strong> в тот момент, когда конкуренты только согласовывают первый макет. Время вывода новых креативов сокращается в 10 раз.</p>
                </div>
                <div class="utp__item">
                    <div class="utp__icon" aria-hidden="true">💰</div>
                    <h3>Бюджет</h3>
                    <p><strong>Получайте результат топ-дизайн-студии</strong>, платя только за автоматизацию процессов. Мы оптимизируем стоимость за счёт использования нейросетей, экономя до 70% вашего бюджета.</p>
                </div>
                ' ?>
            </div>
        </div>
    </section>
    
    <!-- ========================================== -->
    <!-- ====== ПОРТФОЛИО ====== -->
    <!-- ========================================== -->
    <section class="portfolio" id="portfolio" aria-labelledby="portfolio-title">
        <div class="container">
            <h2 class="section-title" id="portfolio-title">
                <?= $portfolioText['title'] ?? 'Примеры <span class="highlight">работ</span>' ?>
            </h2>
            <p class="section-subtitle">
                <?= $portfolioText['content'] ?? 'Реальные проекты MVA Labs — от стратегии до готового продукта. Сайты, приложения и маркетинговые стратегии для бизнеса любого масштаба.' ?>
            </p>
            
            <?php
            // Перебираем категории портфолио
            foreach ($categoryLabels as $categoryKey => $categoryLabel):
                if (empty($portfolioByCategory[$categoryKey])) continue;
            ?>
            <div class="portfolio__category">
                <h3 class="category-title"><?= $categoryLabel ?></h3>
                <p style="color: #9CA3AF; margin-bottom: 20px; font-size: 0.95rem;">
                    <?= $categoryDescriptions[$categoryKey] ?? '' ?>
                </p>
                
                <div class="portfolio__grid">
                    <?php foreach ($portfolioByCategory[$categoryKey] as $item): ?>
                    <div class="portfolio__card">
                        <div class="portfolio__image" style="background: linear-gradient(135deg, <?= htmlspecialchars($item['gradient'] ?? '#2D1B69, #120A2B') ?>);">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <span class="portfolio__placeholder"><?= htmlspecialchars($item['title']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="portfolio__info">
                            <h4><?= htmlspecialchars($item['title']) ?></h4>
                            <p><?= htmlspecialchars($item['description'] ?? '') ?> <strong><?= htmlspecialchars($item['result'] ?? '') ?></strong></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- ===== Блок "Почему выбирают MVA Labs" ===== -->
            <div style="margin-top: 40px; padding: 30px; background: rgba(255,255,255,0.02); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05);">
                <h4 style="color: #D1D5DB; font-size: 1.2rem; margin-bottom: 12px;">Почему MVA Labs выбирают для разработки дизайна?</h4>
                <p style="color: #9CA3AF; line-height: 1.8; font-size: 0.95rem;">
                    В эпоху цифрового маркетинга визуал решает всё. MVA Labs — это <strong>дизайн-агентство нового поколения</strong>, 
                    где искусственный интеллект работает в связке с человеческой креативностью. Мы создаём <strong>сайты, которые продают</strong>, 
                    <strong>приложения, которые удерживают</strong>, и <strong>стратегии, которые масштабируют</strong>. 
                    Наши решения основаны на данных: мы анализируем целевую аудиторию, конкурентов и тренды, 
                    чтобы каждый пиксель работал на ваш бизнес.
                </p>
            </div>
        </div>
    </section>
    
    <!-- ========================================== -->
    <!-- ====== ПАКЕТЫ УСЛУГ ====== -->
    <!-- ========================================== -->
    <section class="prices" id="prices" aria-labelledby="prices-title">
        <div class="container">
            <h2 class="section-title" id="prices-title">
                <?= $pricesText['title'] ?? 'Выберите свой <span class="highlight">пакет</span>' ?>
            </h2>
            <p class="section-subtitle">
                <?= $pricesText['content'] ?? '<strong>Умножьте свой бизнес</strong> с MVA Labs. Выберите подходящий тариф или закажите индивидуальное решение под ваши задачи.' ?>
            </p>
            
            <div class="prices__grid">
                <!-- ===== MVP ===== -->
                <div class="price-card">
                    <div class="price-card__badge">Старт</div>
                    <h3>MVP</h3>
                    <div class="price-card__price">49 900 ₽</div>
                    <ul class="price-card__list">
                        <li>До 10 готовых макетов</li>
                        <li>ИИ-анализ 3 конкурентов</li>
                        <li>1 раунд правок</li>
                        <li>Срок: 2–3 дня</li>
                        <li style="color: #6B7280; font-size: 0.85rem;">Для быстрого старта</li>
                    </ul>
                    <a href="#form" class="btn btn--primary btn--full">Выбрать пакет</a>
                </div>
                
                <!-- ===== A/B TEST ===== -->
                <div class="price-card price-card--popular">
                    <div class="price-card__badge">Хит продаж</div>
                    <h3>A/B TEST</h3>
                    <div class="price-card__price">149 900 ₽</div>
                    <ul class="price-card__list">
                        <li>20 уникальных креативов</li>
                        <li>5 вариантов под рекламу</li>
                        <li>3 формата под каждый</li>
                        <li>ИИ-прогноз конверсии</li>
                        <li>Срок: 5–7 дней</li>
                        <li style="color: #A855F7; font-size: 0.85rem;">🔥 Оптимальный выбор</li>
                    </ul>
                    <a href="#form" class="btn btn--primary btn--full">Выбрать пакет</a>
                </div>
                
                <!-- ===== FULL-STACK ===== -->
                <div class="price-card">
                    <div class="price-card__badge">Максимум</div>
                    <h3>FULL-STACK</h3>
                    <div class="price-card__price">590 000 ₽</div>
                    <ul class="price-card__list">
                        <li>Полная айдентика бренда</li>
                        <li>Сайт + 3 внутренних страницы</li>
                        <li>Приложение (10 экранов)</li>
                        <li>Маркетинг-стратегия на 3 мес</li>
                        <li>Бренд-бук PDF</li>
                        <li>Срок: 3–4 недели</li>
                        <li style="color: #D1D5DB; font-size: 0.85rem;">Полный комплекс</li>
                    </ul>
                    <a href="#form" class="btn btn--primary btn--full">Выбрать пакет</a>
                </div>
            </div>
            
            <!-- ===== SEO-ТЕКСТ ПОД ЦЕНАМИ ===== -->
            <div style="text-align: center; margin-top: 40px; max-width: 800px; margin-left: auto; margin-right: auto;">
                <p style="color: #6B7280; font-size: 0.95rem; line-height: 1.8;">
                    <strong>Не нашли подходящий пакет?</strong> Мы разрабатываем индивидуальные решения под любой бюджет. 
                    Свяжитесь с нами, и мы предложим <strong>оптимальную стратегию для вашего бизнеса</strong> — 
                    от дизайна сайта до полной маркетинговой экосистемы.
                </p>
            </div>
        </div>
    </section>
    
    <!-- ========================================== -->
    <!-- ====== ФОРМА СВЯЗИ ====== -->
    <!-- ========================================== -->
    <section class="form-section" id="form" aria-labelledby="form-title">
        <div class="container">
            <div class="form-section__inner">
                <div class="form-section__info">
                    <h2 id="form-title">Бесплатная <span class="highlight">консультация</span></h2>
                    <p><strong>За 15 минут мы:</strong></p>
                    <ul>
                        <li>✅ Разберём вашу текущую визуальную стратегию</li>
                        <li>✅ Покажем, как ИИ может ускорить ваш процесс в 10 раз</li>
                        <li>✅ Предложим оптимальный пакет под ваш бюджет и задачи</li>
                        <li>✅ Ответим на все вопросы о разработке сайтов, приложений и стратегий</li>
                    </ul>
                    <p style="margin-top: 16px; color: #6B7280; font-size: 0.9rem;">
                        <strong>Работаем с бизнесом любого масштаба</strong> — от стартапов до крупных компаний.
                    </p>
                </div>
                
                <form class="form" id="telegramForm" method="POST" action="bot.php">
                    <input type="text" name="name" placeholder="Ваше имя *" required aria-required="true">
                    <input type="tel" name="phone" placeholder="Ваш телефон *" required aria-required="true">
                    <input type="email" name="email" placeholder="Ваш email" aria-label="Электронная почта">
                    
                    <select name="package" aria-label="Выберите пакет услуг">
                        <option value="">Какой пакет вас интересует?</option>
                        <option value="MVP — 49 900 ₽">MVP — 49 900 ₽</option>
                        <option value="A/B TEST — 149 900 ₽">A/B TEST — 149 900 ₽</option>
                        <option value="FULL-STACK — 590 000 ₽">FULL-STACK — 590 000 ₽</option>
                        <option value="Индивидуальное решение">Индивидуальное решение</option>
                        <option value="Нужна консультация">Нужна консультация</option>
                    </select>
                    
                    <input type="text" name="site" placeholder="Ссылка на ваш сайт или соцсети">
                    <textarea name="message" placeholder="Коротко опишите вашу задачу (опционально)" rows="3"></textarea>
                    
                    <button type="submit" class="btn btn--primary btn--full" id="submitBtn">Отправить заявку</button>
                    <p class="form__note" id="formStatus"></p>
                    <p class="form__note">
                        Или напишите нам в Telegram: <a href="https://t.me/<?= htmlspecialchars($settings['telegram_bot'] ?? 'mva_labs_bot') ?>" target="_blank" rel="noopener noreferrer">@<?= htmlspecialchars($settings['telegram_bot'] ?? 'mva_labs_bot') ?></a>
                    </p>
                </form>
            </div>
        </div>
    </section>
    
    <!-- ========================================== -->
    <!-- ====== ПОДВАЛ (FOOTER) ====== -->
    <!-- ========================================== -->
    <footer class="footer" id="contacts" role="contentinfo">
        <div class="container footer__inner">
            <div class="logo">
                <span class="logo__multiply" aria-hidden="true">×</span>
                <span class="logo__text">MVA <span class="logo__labs">Labs</span></span>
            </div>
            
            <p class="footer__slogan">
                <strong>Умножаем ваш бизнес через искусственный интеллект</strong>
            </p>
            
            <div class="footer__contacts">
                <a href="mailto:<?= htmlspecialchars($settings['email'] ?? 'hello@mvalabs.ru') ?>" aria-label="Написать на email">📧 <?= htmlspecialchars($settings['email'] ?? 'hello@mvalabs.ru') ?></a>
                <a href="tel:<?= htmlspecialchars($settings['phone'] ?? '+7XXXXXXXXXX') ?>" aria-label="Позвонить по телефону">📞 <?= htmlspecialchars($settings['phone'] ?? '+7 (XXX) XXX-XX-XX') ?></a>
                <a href="https://t.me/<?= htmlspecialchars($settings['telegram_bot'] ?? 'mva_labs_bot') ?>" target="_blank" rel="noopener noreferrer" aria-label="Написать в Telegram">💬 Telegram</a>
            </div>
            
            <!-- ===== БОЛЬШОЙ SEO-ТЕКСТ В ПОДВАЛЕ ===== -->
            <div class="footer__seo">
                <h3><?= $footerSeo['title'] ?? 'MVA Labs — агентство маркетинга и дизайна с искусственным интеллектом' ?></h3>
                <div>
                    <?= $footerSeo['content'] ?? '
                    <p><strong>MVA Labs</strong> — это <strong>маркетинговое агентство полного цикла</strong>, которое использует современные нейросети для создания <strong>сайтов, мобильных приложений и визуальных стратегий</strong>. Мы объединяем креативность дизайнеров с мощью искусственного интеллекта, чтобы сокращать сроки выполнения проектов в 3–10 раз и повышать конверсию для наших клиентов.</p>
                    <p style="margin-top: 12px;">Наши услуги включают: <strong>разработку дизайна сайтов</strong> (лендинги, корпоративные порталы, интернет-магазины), <strong>UI/UX дизайн мобильных приложений</strong>, <strong>создание маркетинговых стратегий</strong>, <strong>разработку бренд-буков</strong> и <strong>визуальной айдентики</strong>. Мы работаем с бизнесом любого масштаба — от стартапов до крупных компаний — и адаптируем решения под ваш бюджет.</p>
                    <p style="margin-top: 12px;"><strong>Почему выбирают MVA Labs?</strong> Потому что мы не просто делаем «красиво». Мы создаём дизайн, который <strong>продаёт, удерживает и масштабирует</strong>. Каждый проект начинается с анализа данных: мы изучаем поведение вашей целевой аудитории, конкурентную среду и тренды в вашей нише. Только после этого мы приступаем к визуализации, используя ИИ для генерации сотен вариантов макетов. Это позволяет нам находить <strong>оптимальные визуальные решения</strong> за минимальное время.</p>
                    <p style="margin-top: 12px;">Хотите <strong>умножить свой бизнес</strong> с помощью искусственного интеллекта? Оставьте заявку на бесплатную консультацию, и мы покажем вам, как выглядит <strong>маркетинг будущего</strong> уже сегодня. <strong>MVA Labs — Multiply Your Value by AI.</strong></p>
                    ' ?>
                </div>
            </div>
            
            <p class="footer__copy" style="margin-top: 32px;">
                <?= htmlspecialchars($settings['copyright'] ?? '© 2026 MVA Labs. Все права защищены.') ?>
                <a href="#" style="color: #6B7280; text-decoration: underline;">Политика конфиденциальности</a>
            </p>
        </div>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>