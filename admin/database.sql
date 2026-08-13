-- ==========================================
-- БАЗА ДАННЫХ ДЛЯ MVA LABS ADMIN
-- ==========================================

CREATE DATABASE IF NOT EXISTS `mva_labs`
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `mva_labs`;

-- ==========================================
-- ТАБЛИЦА: АДМИНИСТРАТОРЫ
-- ==========================================
CREATE TABLE `admins` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ВСТАВКА ДЕФОЛТНОГО АДМИНА (пароль: admin123)
INSERT INTO `admins` (`username`, `password_hash`, `email`)
VALUES ('admin', '$2y$10$RNkkEX5QKPJAlnOXtWsGKeg1jkInJ6Dmc9u.4rFSBpN0kH1R61zYy', 'admin@mvalabs.ru');

-- ==========================================
-- ТАБЛИЦА: СТРАНИЦЫ (КОНТЕНТ ЛЕНДИНГА)
-- ==========================================
CREATE TABLE `pages` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `page_key` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `content` LONGTEXT,
    `meta_title` VARCHAR(255),
    `meta_description` TEXT,
    `meta_keywords` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ВСТАВКА СТРАНИЦ
INSERT INTO `pages` (`page_key`, `title`, `meta_title`, `meta_description`) VALUES
('hero', 'Главный экран', 'MVA Labs — Маркетинг и дизайн с ИИ', 'MVA Labs: маркетинговое агентство с использованием ИИ. Создаём сайты, приложения и стратегии.'),
('utp', 'Блок "Что мы умножаем"', 'Что мы умножаем — MVA Labs', 'Креатив, конверсия, скорость и бюджет — умножаем ваш бизнес с помощью ИИ.'),
('portfolio', 'Портфолио', 'Портфолио MVA Labs — сайты, приложения, стратегии', 'Примеры работ MVA Labs. Разработка сайтов, дизайн приложений, маркетинговые стратегии.'),
('prices', 'Цены и пакеты', 'Цены MVA Labs — пакеты услуг', 'Выберите пакет услуг: MVP, A/B TEST или FULL-STACK. Дизайн и маркетинг с ИИ.'),
('footer_seo', 'SEO-текст в подвале', 'MVA Labs — агентство маркетинга и дизайна с ИИ', 'Полное описание услуг и преимуществ MVA Labs для SEO.');

-- ==========================================
-- ТАБЛИЦА: ПОРТФОЛИО
-- ==========================================
CREATE TABLE `portfolio` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `category` ENUM('sites', 'apps', 'strategies') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `result` VARCHAR(255),
    `image_url` VARCHAR(500),
    `gradient` VARCHAR(100),
    `sort_order` INT(11) DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ВСТАВКА ПРИМЕРОВ ПОРТФОЛИО
INSERT INTO `portfolio` (`category`, `title`, `description`, `result`, `gradient`) VALUES
('sites', 'Лендинг для HR-платформы', 'Разработали дизайн сайта с нуля.', '+27% конверсии', '#2D1B69, #120A2B'),
('sites', 'Сайт сети ресторанов', 'Создали фирменный стиль и дизайн главной страницы.', '+34% бронирований', '#1B4332, #081C15'),
('apps', 'Приложение для доставки', 'Разработали UX-архитектуру и дизайн экранов.', '+12% среднего чека', '#0D1B2A, #1B263B'),
('apps', 'Приложение для фитнес-клуба', 'Спроектировали персонализированный интерфейс.', 'Retention +22%', '#1A1A2E, #16213E'),
('strategies', 'Стратегия для магазина косметики', 'Разработали визуальный контент-план и бренд-бук.', 'Вовлечённость +41%', '#4A1942, #2D1B36'),
('strategies', 'Ребрендинг сети кофеен', 'Обновили айдентику, логотип и визуальную стратегию.', 'Средний чек +15%', '#0B3D2E, #1A4D3E');

-- ==========================================
-- ТАБЛИЦА: НАСТРОЙКИ
-- ==========================================
CREATE TABLE `settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(50) NOT NULL UNIQUE,
    `setting_value` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'MVA Labs'),
('site_url', 'http://103.249.134.60'),
('email', 'hello@mvalabs.ru'),
('phone', '+7 (XXX) XXX-XX-XX'),
('telegram_bot', 'mva_labs_bot'),
('telegram_chat_id', '351341132'),
('og_image', '/images/og-image.jpg'),
('copyright', '© 2026 MVA Labs. Все права защищены.');

-- ==========================================
-- ТАБЛИЦА: ЗАЯВКИ
-- ==========================================
CREATE TABLE `leads` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100),
    `package` VARCHAR(100),
    `site` VARCHAR(255),
    `message` TEXT,
    `status` ENUM('new', 'processed', 'closed') DEFAULT 'new',
    `ip` VARCHAR(45),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
