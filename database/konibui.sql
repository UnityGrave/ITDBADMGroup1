-- =============================================================================
-- KONIBUI DATABASE SCHEMA
-- =============================================================================

CREATE DATABASE IF NOT EXISTS konibui;
USE konibui;

-- =============================================================================
-- 1. CORE TABLES (Users, Authentication, Cache, Jobs)
-- =============================================================================

-- Currencies table
CREATE TABLE IF NOT EXISTS `currencies` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(3) NOT NULL UNIQUE COMMENT 'ISO 4217 currency code (e.g., USD, EUR)',
    `name` VARCHAR(100) NOT NULL COMMENT 'Full currency name (e.g., US Dollar)',
    `symbol` VARCHAR(10) NOT NULL COMMENT 'Currency symbol (e.g., $, €, ¥)',
    `exchange_rate` DECIMAL(15,8) NOT NULL DEFAULT 1.00000000 COMMENT 'Exchange rate relative to base currency',
    `is_active` BOOLEAN NOT NULL DEFAULT 1 COMMENT 'Whether this currency is available for use',
    `is_base_currency` BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Whether this is the base currency',
    `formatting_rules` JSON NULL COMMENT 'Currency-specific formatting rules (decimal places, separators)',
    `rate_updated_at` TIMESTAMP NULL COMMENT 'When the exchange rate was last updated',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `idx_active_code` (`is_active`, `code`),
    INDEX `idx_base_currency` (`is_base_currency`),
    INDEX `idx_rate_updated` (`rate_updated_at`)
);

-- Insert default currencies
INSERT INTO `currencies` (`id`, `code`, `name`, `symbol`, `exchange_rate`, `is_active`, `is_base_currency`, `formatting_rules`, `rate_updated_at`, `created_at`, `updated_at`) VALUES
(1, 'USD', 'US Dollar', '$', 1.00000000, 1, 1, '{"decimal_places": 2, "thousands_separator": ",", "decimal_separator": "."}', '2025-07-29 05:42:16', '2025-07-29 05:42:16', '2025-07-29 05:42:16'),
(2, 'CAD', 'Canadian Dollar', 'C$', 1.35000000, 1, 0, '{"decimal_places": 2, "thousands_separator": ",", "decimal_separator": "."}', '2025-07-29 05:42:16', '2025-07-29 05:42:16', '2025-07-29 05:42:16'),
(3, 'EUR', 'Euro', '€', 0.85000000, 1, 0, '{"decimal_places": 2, "thousands_separator": ".", "decimal_separator": ","}', '2025-07-29 05:42:16', '2025-07-29 05:42:16', '2025-07-29 05:42:16'),
(4, 'GBP', 'British Pound', '£', 0.75000000, 1, 0, '{"decimal_places": 2, "thousands_separator": ",", "decimal_separator": "."}', '2025-07-29 05:42:16', '2025-07-29 05:42:16', '2025-07-29 05:42:16'),
(5, 'JPY', 'Japanese Yen', '¥', 150.00000000, 1, 0, '{"decimal_places": 0, "thousands_separator": ",", "decimal_separator": "."}', '2025-07-29 05:42:16', '2025-07-29 05:42:16', '2025-07-29 05:42:16');

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL,
    `preferred_currency` VARCHAR(3) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`preferred_currency`) REFERENCES `currencies`(`code`) ON DELETE SET NULL,
    INDEX `idx_preferred_currency` (`preferred_currency`)
);


INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `preferred_currency`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@example.com', '2025-07-29 05:42:16', '$2y$12$usI6jsU2LMfM9gi392FvZ.dpResp8tDaz7wxPbGHa6RxLmZHSPd0C', NULL, NULL, '2025-07-29 05:42:16', '2025-07-29 05:42:16'),
(2, 'Regular User', 'user@example.com', '2025-07-29 05:42:16', '$2y$12$C4RqdhkR7ZU7Tb.6BMtv2.cehnrARggbjsB7I1kzck6BgSbVcr0lu', NULL, 'CAD', '2025-07-29 05:42:16', '2025-07-29 06:01:42'),
(3, 'Admin User', 'admin@konibui.com', '2025-07-29 05:42:16', '$2y$12$p1sE9quG7W.OxfXM6s3li.WoDodHrjEnszBh0LpmxRrR2W3WSvf4q', 'u7Yb2n7Qz8', NULL, '2025-07-29 05:42:16', '2025-07-29 05:42:16'),
(4, 'Employee User', 'employee@konibui.com', '2025-07-29 05:42:16', '$2y$12$p1sE9quG7W.OxfXM6s3li.WoDodHrjEnszBh0LpmxRrR2W3WSvf4q', 'KfI9DXrQLt', NULL, '2025-07-29 05:42:16', '2025-07-29 05:42:16'),
(5, 'Test Customer', 'test@example.com', '2025-07-29 05:42:16', '$2y$12$p1sE9quG7W.OxfXM6s3li.WoDodHrjEnszBh0LpmxRrR2W3WSvf4q', 'OrBtHExm9K', NULL, '2025-07-29 05:42:16', '2025-07-29 05:42:16');

-- Password reset tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` VARCHAR(255) PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL
);

-- Sessions table
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(255) PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
);

-- Cache tables
CREATE TABLE IF NOT EXISTS `cache` (
    `key` VARCHAR(255) PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
);

CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` VARCHAR(255) PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
);

-- Job tables
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    INDEX `jobs_queue_index` (`queue`)
);

CREATE TABLE IF NOT EXISTS `job_batches` (
    `id` VARCHAR(255) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL,
    `cancelled_at` INT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL
);

CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` VARCHAR(255) NOT NULL UNIQUE,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Migrations table
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL
);

-- =============================================================================
-- 2. ROLE-BASED ACCESS CONTROL
-- =============================================================================

-- Roles table
CREATE TABLE IF NOT EXISTS `roles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '2025-07-29 05:42:15', '2025-07-29 05:42:15'),
(2, 'Employee', '2025-07-29 05:42:15', '2025-07-29 05:42:15'),
(3, 'Customer', '2025-07-29 05:42:15', '2025-07-29 05:42:15');

-- Role-User pivot table
CREATE TABLE IF NOT EXISTS `role_user` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE KEY `role_user_user_id_role_id_unique` (`user_id`, `role_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
);

INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 3, NULL, NULL),
(3, 3, 1, NULL, NULL),
(4, 4, 2, NULL, NULL),
(5, 5, 3, NULL, NULL);

-- =============================================================================
-- 3. TCG CORE TABLES (Cards, Sets, Rarities, Categories)
-- =============================================================================

-- Sets table
CREATE TABLE IF NOT EXISTS `sets` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);

INSERT INTO `sets` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Base Set', NULL, NULL),
(2, 'Jungle', NULL, NULL),
(3, 'Fossil', NULL, NULL),
(4, 'Team Rocket', NULL, NULL),
(5, 'Neo Genesis', NULL, NULL),
(6, 'Sword & Shield', NULL, NULL),
(7, 'Evolving Skies', NULL, NULL),
(8, 'Brilliant Stars', NULL, NULL),
(9, 'Lost Origin', NULL, NULL),
(10, 'Scarlet & Violet', NULL, NULL),
(11, '151', NULL, NULL),
(12, 'Paldea Evolved', NULL, NULL),
(13, 'Hidden Fates', NULL, NULL),
(14, 'Celebrations', NULL, NULL);

-- Rarities table
CREATE TABLE IF NOT EXISTS `rarities` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);

INSERT INTO `rarities` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Common', NULL, NULL),
(2, 'Uncommon', NULL, NULL),
(3, 'Rare', NULL, NULL),
(4, 'Ultra Rare', NULL, NULL),
(5, 'Secret Rare', NULL, NULL);

-- Categories table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);


INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Pokémon', NULL, NULL),
(2, 'Trainer', NULL, NULL),
(3, 'Energy', NULL, NULL),
(4, 'Pokémon ex', NULL, NULL),
(5, 'Pokémon GX', NULL, NULL),
(6, 'Pokémon V', NULL, NULL),
(7, 'Pokémon VMAX', NULL, NULL),
(8, 'Pokémon VSTAR', NULL, NULL),
(9, 'Full Art', NULL, NULL),
(10, 'Alternate Art', NULL, NULL),
(11, 'Rainbow Rare', NULL, NULL),
(12, 'Gold Card', NULL, NULL),
(13, 'Promo', NULL, NULL),
(14, 'Japanese Exclusive', NULL, NULL);

-- Cards table
CREATE TABLE IF NOT EXISTS `cards` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `collector_number` VARCHAR(255) NOT NULL,
    `set_id` BIGINT UNSIGNED NOT NULL,
    `rarity_id` BIGINT UNSIGNED NOT NULL,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`set_id`) REFERENCES `sets`(`id`),
    FOREIGN KEY (`rarity_id`) REFERENCES `rarities`(`id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
);

INSERT INTO `cards` (`id`, `name`, `collector_number`, `set_id`, `rarity_id`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Charizard', '004/102', 1, 4, 1, NULL, NULL),
(2, 'Blastoise', '002/102', 1, 4, 1, NULL, NULL),
(3, 'Venusaur', '015/102', 1, 4, 1, NULL, NULL),
(4, 'Pikachu', '058/102', 1, 1, 1, NULL, NULL),
(5, 'Mewtwo', '010/102', 1, 4, 1, NULL, NULL),
(6, 'Scyther', '010/64', 2, 3, 1, NULL, NULL),
(7, 'Vaporeon', '012/64', 2, 3, 1, NULL, NULL),
(8, 'Jolteon', '004/64', 2, 3, 1, NULL, NULL),
(9, 'Charizard V', '019/189', 6, 4, 6, NULL, NULL),
(10, 'Pikachu V', '043/185', 6, 4, 6, NULL, NULL),
(11, 'Rayquaza V', '100/203', 7, 4, 6, NULL, NULL),
(12, 'Charizard VMAX', '020/189', 6, 4, 7, NULL, NULL),
(13, 'Pikachu VMAX', '044/185', 6, 4, 7, NULL, NULL),
(14, 'Charizard ex', '006/165', 10, 4, 4, NULL, NULL),
(15, 'Miraidon ex', '081/198', 10, 4, 4, NULL, NULL),
(16, 'Rayquaza (Amazing Rare)', '109/185', 8, 5, 1, NULL, NULL),
(17, 'Umbreon VMAX (Alt Art)', '215/203', 7, 5, 10, NULL, NULL),
(18, 'Charizard VMAX (Rainbow)', '074/073', 14, 5, 11, NULL, NULL),
(19, 'Fire Energy', '092/102', 1, 1, 3, NULL, NULL),
(20, 'Water Energy', '093/102', 1, 1, 3, NULL, NULL),
(21, 'Lightning Energy', '094/102', 1, 1, 3, NULL, NULL),
(22, 'Psychic Energy', '095/102', 1, 1, 3, NULL, NULL),
(23, 'Double Colorless Energy', '100/102', 1, 2, 3, NULL, NULL),
(24, 'Rainbow Energy', '017/109', 4, 3, 3, NULL, NULL),
(25, 'Professor Oak', '088/102', 1, 2, 2, NULL, NULL),
(26, 'Computer Search', '071/102', 1, 3, 2, NULL, NULL),
(27, 'Professor\'s Research', '178/202', 6, 2, 2, NULL, NULL),
(28, 'Professor\'s Research (Full Art)', '201/202', 6, 4, 9, NULL, NULL),
(29, 'Birthday Pikachu', 'PROMO', 14, 5, 13, NULL, NULL),
(30, 'Alakazam (Error)', '001/102', 1, 5, 1, NULL, NULL);

-- =============================================================================
-- 4. PRODUCTS AND INVENTORY TABLES
-- =============================================================================

-- Products table
CREATE TABLE IF NOT EXISTS `products` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `card_id` BIGINT UNSIGNED NOT NULL,
    `condition` VARCHAR(255) NOT NULL, -- ENUM: NM, LP, MP, HP, DMG (enforced in app/model)
    `base_currency_code` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `base_price_cents` BIGINT NOT NULL,
    `sku` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`card_id`) REFERENCES `cards`(`id`),
    FOREIGN KEY (`base_currency_code`) REFERENCES `currencies`(`code`),
    INDEX `idx_base_currency_code` (`base_currency_code`)
);

INSERT INTO `products` (`id`, `card_id`, `condition`, `base_currency_code`, `base_price_cents`, `sku`, `created_at`, `updated_at`) VALUES
(1, 1, 'NM', 'USD', 175525, 'PKM-001-NM', NULL, NULL),
(2, 1, 'LP', 'USD', 190628, 'PKM-001-LP', NULL, NULL),
(3, 1, 'MP', 'USD', 162780, 'PKM-001-MP', NULL, NULL),
(4, 1, 'HP', 'USD', 73833, 'PKM-001-HP', NULL, NULL),
(5, 1, 'DMG', 'USD', 74680, 'PKM-001-DM', NULL, NULL),
(6, 2, 'NM', 'USD', 12209, 'PKM-002-NM', NULL, NULL),
(7, 2, 'LP', 'USD', 9290, 'PKM-002-LP', NULL, NULL),
(8, 2, 'MP', 'USD', 7503, 'PKM-002-MP', NULL, NULL),
(9, 2, 'HP', 'USD', 5143, 'PKM-002-HP', NULL, NULL),
(10, 2, 'DMG', 'USD', 3731, 'PKM-002-DM', NULL, NULL),
(11, 3, 'NM', 'USD', 8172, 'PKM-003-NM', NULL, NULL),
(12, 3, 'LP', 'USD', 5346, 'PKM-003-LP', NULL, NULL),
(13, 3, 'MP', 'USD', 6598, 'PKM-003-MP', NULL, NULL),
(14, 3, 'HP', 'USD', 4508, 'PKM-003-HP', NULL, NULL),
(15, 3, 'DMG', 'USD', 1893, 'PKM-003-DM', NULL, NULL),
(16, 4, 'NM', 'USD', 135, 'PKM-004-NM', NULL, NULL),
(17, 4, 'LP', 'USD', 168, 'PKM-004-LP', NULL, NULL),
(18, 4, 'MP', 'USD', 39, 'PKM-004-MP', NULL, NULL),
(19, 4, 'HP', 'USD', 65, 'PKM-004-HP', NULL, NULL),
(20, 4, 'DMG', 'USD', 50, 'PKM-004-DM', NULL, NULL),
(21, 5, 'NM', 'USD', 14470, 'PKM-005-NM', NULL, NULL),
(22, 5, 'LP', 'USD', 16077, 'PKM-005-LP', NULL, NULL),
(23, 5, 'MP', 'USD', 8902, 'PKM-005-MP', NULL, NULL),
(24, 5, 'HP', 'USD', 5568, 'PKM-005-HP', NULL, NULL),
(25, 5, 'DMG', 'USD', 3017, 'PKM-005-DM', NULL, NULL),
(26, 6, 'NM', 'USD', 530, 'PKM-006-NM', NULL, NULL),
(27, 6, 'LP', 'USD', 841, 'PKM-006-LP', NULL, NULL),
(28, 6, 'MP', 'USD', 467, 'PKM-006-MP', NULL, NULL),
(29, 6, 'HP', 'USD', 329, 'PKM-006-HP', NULL, NULL),
(30, 6, 'DMG', 'USD', 139, 'PKM-006-DM', NULL, NULL),
(31, 7, 'NM', 'USD', 1086, 'PKM-007-NM', NULL, NULL),
(32, 7, 'LP', 'USD', 489, 'PKM-007-LP', NULL, NULL),
(33, 7, 'MP', 'USD', 359, 'PKM-007-MP', NULL, NULL),
(34, 7, 'HP', 'USD', 371, 'PKM-007-HP', NULL, NULL),
(35, 7, 'DMG', 'USD', 144, 'PKM-007-DM', NULL, NULL),
(36, 8, 'NM', 'USD', 1124, 'PKM-008-NM', NULL, NULL),
(37, 8, 'LP', 'USD', 1000, 'PKM-008-LP', NULL, NULL),
(38, 8, 'MP', 'USD', 639, 'PKM-008-MP', NULL, NULL),
(39, 8, 'HP', 'USD', 229, 'PKM-008-HP', NULL, NULL),
(40, 8, 'DMG', 'USD', 157, 'PKM-008-DM', NULL, NULL),
(41, 9, 'NM', 'USD', 24613, 'PKM-009-NM', NULL, NULL),
(42, 9, 'LP', 'USD', 20522, 'PKM-009-LP', NULL, NULL),
(43, 9, 'MP', 'USD', 11324, 'PKM-009-MP', NULL, NULL),
(44, 9, 'HP', 'USD', 9117, 'PKM-009-HP', NULL, NULL),
(45, 9, 'DMG', 'USD', 6206, 'PKM-009-DM', NULL, NULL),
(46, 10, 'NM', 'USD', 2695, 'PKM-010-NM', NULL, NULL),
(47, 10, 'LP', 'USD', 1634, 'PKM-010-LP', NULL, NULL),
(48, 10, 'MP', 'USD', 681, 'PKM-010-MP', NULL, NULL),
(49, 10, 'HP', 'USD', 866, 'PKM-010-HP', NULL, NULL),
(50, 10, 'DMG', 'USD', 251, 'PKM-010-DM', NULL, NULL),
(51, 11, 'NM', 'USD', 1204, 'PKM-011-NM', NULL, NULL),
(52, 11, 'LP', 'USD', 1012, 'PKM-011-LP', NULL, NULL),
(53, 11, 'MP', 'USD', 845, 'PKM-011-MP', NULL, NULL),
(54, 11, 'HP', 'USD', 595, 'PKM-011-HP', NULL, NULL),
(55, 11, 'DMG', 'USD', 630, 'PKM-011-DM', NULL, NULL),
(56, 12, 'NM', 'USD', 21170, 'PKM-012-NM', NULL, NULL),
(57, 12, 'LP', 'USD', 37247, 'PKM-012-LP', NULL, NULL),
(58, 12, 'MP', 'USD', 24043, 'PKM-012-MP', NULL, NULL),
(59, 12, 'HP', 'USD', 13089, 'PKM-012-HP', NULL, NULL),
(60, 12, 'DMG', 'USD', 6143, 'PKM-012-DM', NULL, NULL),
(61, 13, 'NM', 'USD', 1380, 'PKM-013-NM', NULL, NULL),
(62, 13, 'LP', 'USD', 2320, 'PKM-013-LP', NULL, NULL),
(63, 13, 'MP', 'USD', 1576, 'PKM-013-MP', NULL, NULL),
(64, 13, 'HP', 'USD', 622, 'PKM-013-HP', NULL, NULL),
(65, 13, 'DMG', 'USD', 589, 'PKM-013-DM', NULL, NULL),
(66, 14, 'NM', 'USD', 24821, 'PKM-014-NM', NULL, NULL),
(67, 14, 'LP', 'USD', 15935, 'PKM-014-LP', NULL, NULL),
(68, 14, 'MP', 'USD', 13797, 'PKM-014-MP', NULL, NULL),
(69, 14, 'HP', 'USD', 6955, 'PKM-014-HP', NULL, NULL),
(70, 14, 'DMG', 'USD', 4846, 'PKM-014-DM', NULL, NULL),
(71, 15, 'NM', 'USD', 1086, 'PKM-015-NM', NULL, NULL),
(72, 15, 'LP', 'USD', 1473, 'PKM-015-LP', NULL, NULL),
(73, 15, 'MP', 'USD', 1248, 'PKM-015-MP', NULL, NULL),
(74, 15, 'HP', 'USD', 859, 'PKM-015-HP', NULL, NULL),
(75, 15, 'DMG', 'USD', 469, 'PKM-015-DM', NULL, NULL),
(76, 16, 'NM', 'USD', 6514, 'PKM-016-NM', NULL, NULL),
(77, 16, 'LP', 'USD', 5364, 'PKM-016-LP', NULL, NULL),
(78, 16, 'MP', 'USD', 3770, 'PKM-016-MP', NULL, NULL),
(79, 16, 'HP', 'USD', 2838, 'PKM-016-HP', NULL, NULL),
(80, 16, 'DMG', 'USD', 682, 'PKM-016-DM', NULL, NULL),
(81, 17, 'NM', 'USD', 16939, 'PKM-017-NM', NULL, NULL),
(82, 17, 'LP', 'USD', 15242, 'PKM-017-LP', NULL, NULL),
(83, 17, 'MP', 'USD', 10850, 'PKM-017-MP', NULL, NULL),
(84, 17, 'HP', 'USD', 5271, 'PKM-017-HP', NULL, NULL),
(85, 17, 'DMG', 'USD', 3503, 'PKM-017-DM', NULL, NULL),
(86, 18, 'NM', 'USD', 20271, 'PKM-018-NM', NULL, NULL),
(87, 18, 'LP', 'USD', 20363, 'PKM-018-LP', NULL, NULL),
(88, 18, 'MP', 'USD', 16634, 'PKM-018-MP', NULL, NULL),
(89, 18, 'HP', 'USD', 8015, 'PKM-018-HP', NULL, NULL),
(90, 18, 'DMG', 'USD', 3950, 'PKM-018-DM', NULL, NULL),
(91, 19, 'NM', 'USD', 48468, 'PKM-019-NM', NULL, NULL),
(92, 19, 'LP', 'USD', 36214, 'PKM-019-LP', NULL, NULL),
(93, 19, 'MP', 'USD', 26172, 'PKM-019-MP', NULL, NULL),
(94, 19, 'HP', 'USD', 11533, 'PKM-019-HP', NULL, NULL),
(95, 19, 'DMG', 'USD', 10044, 'PKM-019-DM', NULL, NULL),
(96, 20, 'NM', 'USD', 109, 'PKM-020-NM', NULL, NULL),
(97, 20, 'LP', 'USD', 116, 'PKM-020-LP', NULL, NULL),
(98, 20, 'MP', 'USD', 52, 'PKM-020-MP', NULL, NULL),
(99, 20, 'HP', 'USD', 49, 'PKM-020-HP', NULL, NULL),
(100, 20, 'DMG', 'USD', 50, 'PKM-020-DM', NULL, NULL),
(101, 21, 'NM', 'USD', 174, 'PKM-021-NM', NULL, NULL),
(102, 21, 'LP', 'USD', 109, 'PKM-021-LP', NULL, NULL),
(103, 21, 'MP', 'USD', 69, 'PKM-021-MP', NULL, NULL),
(104, 21, 'HP', 'USD', 90, 'PKM-021-HP', NULL, NULL),
(105, 21, 'DMG', 'USD', 42, 'PKM-021-DM', NULL, NULL),
(106, 22, 'NM', 'USD', 194, 'PKM-022-NM', NULL, NULL),
(107, 22, 'LP', 'USD', 48, 'PKM-022-LP', NULL, NULL),
(108, 22, 'MP', 'USD', 86, 'PKM-022-MP', NULL, NULL),
(109, 22, 'HP', 'USD', 85, 'PKM-022-HP', NULL, NULL),
(110, 22, 'DMG', 'USD', 25, 'PKM-022-DM', NULL, NULL),
(111, 23, 'NM', 'USD', 137, 'PKM-023-NM', NULL, NULL),
(112, 23, 'LP', 'USD', 128, 'PKM-023-LP', NULL, NULL),
(113, 23, 'MP', 'USD', 112, 'PKM-023-MP', NULL, NULL),
(114, 23, 'HP', 'USD', 88, 'PKM-023-HP', NULL, NULL),
(115, 23, 'DMG', 'USD', 25, 'PKM-023-DM', NULL, NULL),
(116, 24, 'NM', 'USD', 546, 'PKM-024-NM', NULL, NULL),
(117, 24, 'LP', 'USD', 466, 'PKM-024-LP', NULL, NULL),
(118, 24, 'MP', 'USD', 175, 'PKM-024-MP', NULL, NULL),
(119, 24, 'HP', 'USD', 248, 'PKM-024-HP', NULL, NULL),
(120, 24, 'DMG', 'USD', 89, 'PKM-024-DM', NULL, NULL),
(121, 25, 'NM', 'USD', 209, 'PKM-025-NM', NULL, NULL),
(122, 25, 'LP', 'USD', 477, 'PKM-025-LP', NULL, NULL),
(123, 25, 'MP', 'USD', 383, 'PKM-025-MP', NULL, NULL),
(124, 25, 'HP', 'USD', 171, 'PKM-025-HP', NULL, NULL),
(125, 25, 'DMG', 'USD', 99, 'PKM-025-DM', NULL, NULL),
(126, 26, 'NM', 'USD', 272, 'PKM-026-NM', NULL, NULL),
(127, 26, 'LP', 'USD', 509, 'PKM-026-LP', NULL, NULL),
(128, 26, 'MP', 'USD', 247, 'PKM-026-MP', NULL, NULL),
(129, 26, 'HP', 'USD', 155, 'PKM-026-HP', NULL, NULL),
(130, 26, 'DMG', 'USD', 121, 'PKM-026-DM', NULL, NULL),
(131, 27, 'NM', 'USD', 25685, 'PKM-027-NM', NULL, NULL),
(132, 27, 'LP', 'USD', 17195, 'PKM-027-LP', NULL, NULL),
(133, 27, 'MP', 'USD', 16619, 'PKM-027-MP', NULL, NULL),
(134, 27, 'HP', 'USD', 9069, 'PKM-027-HP', NULL, NULL),
(135, 27, 'DMG', 'USD', 5337, 'PKM-027-DM', NULL, NULL),
(136, 28, 'NM', 'USD', 26977, 'PKM-028-NM', NULL, NULL),
(137, 28, 'LP', 'USD', 35096, 'PKM-028-LP', NULL, NULL),
(138, 28, 'MP', 'USD', 29843, 'PKM-028-MP', NULL, NULL),
(139, 28, 'HP', 'USD', 16363, 'PKM-028-HP', NULL, NULL),
(140, 28, 'DMG', 'USD', 7486, 'PKM-028-DM', NULL, NULL),
(141, 1, 'NM', 'USD', 800000, 'PKM-001-PSA10', NULL, NULL),
(142, 27, 'NM', 'USD', 35000, 'PKM-027-JP-NM', NULL, NULL);


-- Inventory table
CREATE TABLE IF NOT EXISTS `inventory` (
    `product_id` BIGINT UNSIGNED PRIMARY KEY,
    `stock` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- =============================================================================
-- 5. SHOPPING CART TABLES
-- =============================================================================

-- Cart items table
CREATE TABLE IF NOT EXISTS `cart_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE KEY `cart_items_user_id_product_id_unique` (`user_id`, `product_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- =============================================================================
-- 6. ORDER MANAGEMENT TABLES
-- =============================================================================

-- Orders table
CREATE TABLE IF NOT EXISTS `orders` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(255) NOT NULL UNIQUE,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
    `payment_method` ENUM('cod', 'credit_card', 'paypal') NOT NULL DEFAULT 'cod',
    `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    `subtotal` DECIMAL(10, 2) NOT NULL,
    `tax_amount` DECIMAL(10, 2) NOT NULL,
    `shipping_cost` DECIMAL(10, 2) NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `currency_code` VARCHAR(3) NOT NULL,
    `exchange_rate` DECIMAL(18,8) NOT NULL,
    `total_in_base_currency` BIGINT NOT NULL,
    `shipping_first_name` VARCHAR(255) NOT NULL,
    `shipping_last_name` VARCHAR(255) NOT NULL,
    `shipping_email` VARCHAR(255) NOT NULL,
    `shipping_phone` VARCHAR(255) NOT NULL,
    `shipping_address_line_1` VARCHAR(255) NOT NULL,
    `shipping_address_line_2` VARCHAR(255) NULL,
    `shipping_city` VARCHAR(255) NOT NULL,
    `shipping_state` VARCHAR(255) NOT NULL,
    `shipping_postal_code` VARCHAR(255) NOT NULL,
    `shipping_country` VARCHAR(2) NOT NULL DEFAULT 'US',
    `special_instructions` TEXT NULL,
    `notes` TEXT NULL,
    `shipped_at` TIMESTAMP NULL,
    `delivered_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `orders_user_id_status_index` (`user_id`, `status`),
    INDEX `orders_order_number_index` (`order_number`),
    INDEX `orders_created_at_index` (`created_at`),
    INDEX `idx_currency_code` (`currency_code`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`currency_code`) REFERENCES `currencies`(`code`) ON DELETE RESTRICT
);

-- Order items table
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `product_sku` VARCHAR(255) NOT NULL,
    `unit_price` DECIMAL(10, 2) NOT NULL,
    `price_in_base_currency` BIGINT NOT NULL,
    `quantity` INT NOT NULL,
    `total_price` DECIMAL(10, 2) NOT NULL,
    `product_details` JSON NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `order_items_order_id_index` (`order_id`),
    INDEX `order_items_product_id_index` (`product_id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- =============================================================================
-- 7. SUPPORT TABLES (Logging, Auditing, Analytics)
-- =============================================================================

-- Order status log
CREATE TABLE IF NOT EXISTS `order_status_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `old_status` VARCHAR(50) NULL,
    `new_status` VARCHAR(50) NOT NULL,
    `changed_by` BIGINT UNSIGNED NULL,
    `change_reason` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`)
);

-- Price history
CREATE TABLE IF NOT EXISTS `price_history` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `old_price` DECIMAL(10,2) NOT NULL,
    `new_price` DECIMAL(10,2) NOT NULL,
    `change_amount` DECIMAL(10,2) NOT NULL,
    `change_percentage` DECIMAL(5,2) NULL,
    `changed_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`)
);

-- Stock alerts
CREATE TABLE IF NOT EXISTS `stock_alerts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `product_name` VARCHAR(255) NULL,
    `current_stock` INT NOT NULL,
    `threshold_value` INT NOT NULL,
    `alert_type` ENUM('low_stock', 'out_of_stock') NOT NULL,
    `message` TEXT NULL,
    `is_resolved` BOOLEAN DEFAULT FALSE,
    `resolved_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

-- User activity log
CREATE TABLE IF NOT EXISTS `user_activity_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `activity_type` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    `related_table` VARCHAR(50) NULL,
    `related_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_activity` (`user_id`, `created_at`),
    INDEX `idx_activity_type` (`activity_type`, `created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

-- Search index updates
CREATE TABLE IF NOT EXISTS `search_index_updates` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `table_name` VARCHAR(50) NOT NULL,
    `record_id` BIGINT UNSIGNED NOT NULL,
    `action` ENUM('insert', 'update', 'delete') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_pending_updates` (`table_name`, `record_id`)
);

INSERT INTO `search_index_updates` (`id`, `table_name`, `record_id`, `action`, `created_at`) VALUES
(1, 'products', 1, 'insert', '2025-07-29 05:42:15'),
(2, 'products', 2, 'insert', '2025-07-29 05:42:15'),
(3, 'products', 3, 'insert', '2025-07-29 05:42:15'),
(4, 'products', 4, 'insert', '2025-07-29 05:42:15'),
(5, 'products', 5, 'insert', '2025-07-29 05:42:15'),
(6, 'products', 6, 'insert', '2025-07-29 05:42:15'),
(7, 'products', 7, 'insert', '2025-07-29 05:42:15'),
(8, 'products', 8, 'insert', '2025-07-29 05:42:15'),
(9, 'products', 9, 'insert', '2025-07-29 05:42:15'),
(10, 'products', 10, 'insert', '2025-07-29 05:42:15'),
(11, 'products', 11, 'insert', '2025-07-29 05:42:15'),
(12, 'products', 12, 'insert', '2025-07-29 05:42:15'),
(13, 'products', 13, 'insert', '2025-07-29 05:42:15'),
(14, 'products', 14, 'insert', '2025-07-29 05:42:15'),
(15, 'products', 15, 'insert', '2025-07-29 05:42:15'),
(16, 'products', 16, 'insert', '2025-07-29 05:42:15'),
(17, 'products', 17, 'insert', '2025-07-29 05:42:15'),
(18, 'products', 18, 'insert', '2025-07-29 05:42:15'),
(19, 'products', 19, 'insert', '2025-07-29 05:42:15'),
(20, 'products', 20, 'insert', '2025-07-29 05:42:15'),
(21, 'products', 21, 'insert', '2025-07-29 05:42:15'),
(22, 'products', 22, 'insert', '2025-07-29 05:42:15'),
(23, 'products', 23, 'insert', '2025-07-29 05:42:15'),
(24, 'products', 24, 'insert', '2025-07-29 05:42:15'),
(25, 'products', 25, 'insert', '2025-07-29 05:42:15'),
(26, 'products', 26, 'insert', '2025-07-29 05:42:15'),
(27, 'products', 27, 'insert', '2025-07-29 05:42:15'),
(28, 'products', 28, 'insert', '2025-07-29 05:42:15'),
(29, 'products', 29, 'insert', '2025-07-29 05:42:15'),
(30, 'products', 30, 'insert', '2025-07-29 05:42:15'),
(31, 'products', 31, 'insert', '2025-07-29 05:42:15'),
(32, 'products', 32, 'insert', '2025-07-29 05:42:15'),
(33, 'products', 33, 'insert', '2025-07-29 05:42:15'),
(34, 'products', 34, 'insert', '2025-07-29 05:42:15'),
(35, 'products', 35, 'insert', '2025-07-29 05:42:15'),
(36, 'products', 36, 'insert', '2025-07-29 05:42:15'),
(37, 'products', 37, 'insert', '2025-07-29 05:42:15'),
(38, 'products', 38, 'insert', '2025-07-29 05:42:15'),
(39, 'products', 39, 'insert', '2025-07-29 05:42:15'),
(40, 'products', 40, 'insert', '2025-07-29 05:42:15'),
(41, 'products', 41, 'insert', '2025-07-29 05:42:15'),
(42, 'products', 42, 'insert', '2025-07-29 05:42:15'),
(43, 'products', 43, 'insert', '2025-07-29 05:42:15'),
(44, 'products', 44, 'insert', '2025-07-29 05:42:15'),
(45, 'products', 45, 'insert', '2025-07-29 05:42:15'),
(46, 'products', 46, 'insert', '2025-07-29 05:42:15'),
(47, 'products', 47, 'insert', '2025-07-29 05:42:15'),
(48, 'products', 48, 'insert', '2025-07-29 05:42:15'),
(49, 'products', 49, 'insert', '2025-07-29 05:42:15'),
(50, 'products', 50, 'insert', '2025-07-29 05:42:15'),
(51, 'products', 51, 'insert', '2025-07-29 05:42:15'),
(52, 'products', 52, 'insert', '2025-07-29 05:42:15'),
(53, 'products', 53, 'insert', '2025-07-29 05:42:15'),
(54, 'products', 54, 'insert', '2025-07-29 05:42:15'),
(55, 'products', 55, 'insert', '2025-07-29 05:42:15'),
(56, 'products', 56, 'insert', '2025-07-29 05:42:15'),
(57, 'products', 57, 'insert', '2025-07-29 05:42:15'),
(58, 'products', 58, 'insert', '2025-07-29 05:42:15'),
(59, 'products', 59, 'insert', '2025-07-29 05:42:15'),
(60, 'products', 60, 'insert', '2025-07-29 05:42:15'),
(61, 'products', 61, 'insert', '2025-07-29 05:42:15'),
(62, 'products', 62, 'insert', '2025-07-29 05:42:15'),
(63, 'products', 63, 'insert', '2025-07-29 05:42:15'),
(64, 'products', 64, 'insert', '2025-07-29 05:42:15'),
(65, 'products', 65, 'insert', '2025-07-29 05:42:15'),
(66, 'products', 66, 'insert', '2025-07-29 05:42:15'),
(67, 'products', 67, 'insert', '2025-07-29 05:42:15'),
(68, 'products', 68, 'insert', '2025-07-29 05:42:15'),
(69, 'products', 69, 'insert', '2025-07-29 05:42:15'),
(70, 'products', 70, 'insert', '2025-07-29 05:42:15'),
(71, 'products', 71, 'insert', '2025-07-29 05:42:15'),
(72, 'products', 72, 'insert', '2025-07-29 05:42:15'),
(73, 'products', 73, 'insert', '2025-07-29 05:42:15'),
(74, 'products', 74, 'insert', '2025-07-29 05:42:15'),
(75, 'products', 75, 'insert', '2025-07-29 05:42:15'),
(76, 'products', 76, 'insert', '2025-07-29 05:42:15'),
(77, 'products', 77, 'insert', '2025-07-29 05:42:15'),
(78, 'products', 78, 'insert', '2025-07-29 05:42:15'),
(79, 'products', 79, 'insert', '2025-07-29 05:42:15'),
(80, 'products', 80, 'insert', '2025-07-29 05:42:15'),
(81, 'products', 81, 'insert', '2025-07-29 05:42:15'),
(82, 'products', 82, 'insert', '2025-07-29 05:42:15'),
(83, 'products', 83, 'insert', '2025-07-29 05:42:15'),
(84, 'products', 84, 'insert', '2025-07-29 05:42:15'),
(85, 'products', 85, 'insert', '2025-07-29 05:42:15'),
(86, 'products', 86, 'insert', '2025-07-29 05:42:15'),
(87, 'products', 87, 'insert', '2025-07-29 05:42:15'),
(88, 'products', 88, 'insert', '2025-07-29 05:42:15'),
(89, 'products', 89, 'insert', '2025-07-29 05:42:15'),
(90, 'products', 90, 'insert', '2025-07-29 05:42:15'),
(91, 'products', 91, 'insert', '2025-07-29 05:42:15'),
(92, 'products', 92, 'insert', '2025-07-29 05:42:15'),
(93, 'products', 93, 'insert', '2025-07-29 05:42:15'),
(94, 'products', 94, 'insert', '2025-07-29 05:42:15'),
(95, 'products', 95, 'insert', '2025-07-29 05:42:15'),
(96, 'products', 96, 'insert', '2025-07-29 05:42:15'),
(97, 'products', 97, 'insert', '2025-07-29 05:42:15'),
(98, 'products', 98, 'insert', '2025-07-29 05:42:15'),
(99, 'products', 99, 'insert', '2025-07-29 05:42:15'),
(100, 'products', 100, 'insert', '2025-07-29 05:42:15'),
(101, 'products', 101, 'insert', '2025-07-29 05:42:15'),
(102, 'products', 102, 'insert', '2025-07-29 05:42:15'),
(103, 'products', 103, 'insert', '2025-07-29 05:42:15'),
(104, 'products', 104, 'insert', '2025-07-29 05:42:15'),
(105, 'products', 105, 'insert', '2025-07-29 05:42:15'),
(106, 'products', 106, 'insert', '2025-07-29 05:42:15'),
(107, 'products', 107, 'insert', '2025-07-29 05:42:15'),
(108, 'products', 108, 'insert', '2025-07-29 05:42:15'),
(109, 'products', 109, 'insert', '2025-07-29 05:42:15'),
(110, 'products', 110, 'insert', '2025-07-29 05:42:15'),
(111, 'products', 111, 'insert', '2025-07-29 05:42:15'),
(112, 'products', 112, 'insert', '2025-07-29 05:42:15'),
(113, 'products', 113, 'insert', '2025-07-29 05:42:15'),
(114, 'products', 114, 'insert', '2025-07-29 05:42:15'),
(115, 'products', 115, 'insert', '2025-07-29 05:42:15'),
(116, 'products', 116, 'insert', '2025-07-29 05:42:15'),
(117, 'products', 117, 'insert', '2025-07-29 05:42:15'),
(118, 'products', 118, 'insert', '2025-07-29 05:42:15'),
(119, 'products', 119, 'insert', '2025-07-29 05:42:15'),
(120, 'products', 120, 'insert', '2025-07-29 05:42:15'),
(121, 'products', 121, 'insert', '2025-07-29 05:42:15'),
(122, 'products', 122, 'insert', '2025-07-29 05:42:15'),
(123, 'products', 123, 'insert', '2025-07-29 05:42:15'),
(124, 'products', 124, 'insert', '2025-07-29 05:42:15'),
(125, 'products', 125, 'insert', '2025-07-29 05:42:15'),
(126, 'products', 126, 'insert', '2025-07-29 05:42:15'),
(127, 'products', 127, 'insert', '2025-07-29 05:42:15'),
(128, 'products', 128, 'insert', '2025-07-29 05:42:15'),
(129, 'products', 129, 'insert', '2025-07-29 05:42:15'),
(130, 'products', 130, 'insert', '2025-07-29 05:42:15'),
(131, 'products', 131, 'insert', '2025-07-29 05:42:15'),
(132, 'products', 132, 'insert', '2025-07-29 05:42:15'),
(133, 'products', 133, 'insert', '2025-07-29 05:42:15'),
(134, 'products', 134, 'insert', '2025-07-29 05:42:15'),
(135, 'products', 135, 'insert', '2025-07-29 05:42:15'),
(136, 'products', 136, 'insert', '2025-07-29 05:42:15'),
(137, 'products', 137, 'insert', '2025-07-29 05:42:15'),
(138, 'products', 138, 'insert', '2025-07-29 05:42:15'),
(139, 'products', 139, 'insert', '2025-07-29 05:42:15'),
(140, 'products', 140, 'insert', '2025-07-29 05:42:15'),
(141, 'products', 141, 'insert', '2025-07-29 05:42:15'),
(142, 'products', 142, 'insert', '2025-07-29 05:42:15');

-- Inventory logs
CREATE TABLE IF NOT EXISTS `inventory_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `old_stock` INT NOT NULL,
    `new_stock` INT NOT NULL,
    `change_amount` INT NOT NULL,
    `reason` VARCHAR(255) NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_product_updates` (`product_id`, `created_at`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`)
);

-- Refunds table
CREATE TABLE IF NOT EXISTS `refunds` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `reason` TEXT NULL,
    `processed_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order_refunds` (`order_id`, `created_at`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
    FOREIGN KEY (`processed_by`) REFERENCES `users`(`id`)
);

-- Archived orders (for performance)
CREATE TABLE IF NOT EXISTS `archived_orders` LIKE `orders`;
ALTER TABLE `archived_orders` 
    ADD COLUMN `archived_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD INDEX `idx_archived_date` (`archived_at`);

-- Archived order items
CREATE TABLE IF NOT EXISTS `archived_order_items` LIKE `order_items`;
ALTER TABLE `archived_order_items` 
    ADD COLUMN `archived_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD INDEX `idx_archived_date` (`archived_at`);

-- Inventory adjustments
CREATE TABLE IF NOT EXISTS `inventory_adjustments` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `adjustment_type` ENUM('reduce', 'restore') NOT NULL,
    `reason` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_order_adjustments` (`order_id`, `created_at`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`)
);

-- Archived inventory adjustments
CREATE TABLE IF NOT EXISTS `archived_inventory_adjustments` LIKE `inventory_adjustments`;
ALTER TABLE `archived_inventory_adjustments` 
    ADD COLUMN `archived_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD INDEX `idx_archived_date` (`archived_at`);

-- =============================================================================
-- 8. STORED PROCEDURES
-- =============================================================================

DELIMITER //

-- sp_PlaceOrder - Complete order placement with inventory management
CREATE PROCEDURE sp_PlaceOrder(
                IN p_user_id BIGINT UNSIGNED,
                IN p_shipping_first_name VARCHAR(50),
                IN p_shipping_last_name VARCHAR(50),
                IN p_shipping_email VARCHAR(100),
                IN p_shipping_phone VARCHAR(20),
                IN p_shipping_address_line_1 VARCHAR(255),
                IN p_shipping_address_line_2 VARCHAR(255),
                IN p_shipping_city VARCHAR(100),
                IN p_shipping_state VARCHAR(50),
                IN p_shipping_postal_code VARCHAR(20),
                IN p_shipping_country VARCHAR(50),
                IN p_payment_method VARCHAR(50),
                IN p_special_instructions TEXT,
                IN p_tax_rate DECIMAL(5,4),
                IN p_shipping_cost DECIMAL(10,2),
                OUT p_order_id BIGINT UNSIGNED,
                OUT p_order_number VARCHAR(20),
                OUT p_total_amount DECIMAL(10,2),
                OUT p_status VARCHAR(20),
                OUT p_message TEXT
            )
            BEGIN
                -- All declarations must come first
                DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
                DECLARE v_tax_amount DECIMAL(10,2) DEFAULT 0;
                DECLARE v_total_amount DECIMAL(10,2) DEFAULT 0;
                DECLARE v_product_id BIGINT UNSIGNED;
                DECLARE v_quantity INT;
                DECLARE v_unit_price DECIMAL(10,2);
                DECLARE v_stock INT;
                DECLARE v_product_name VARCHAR(255);
                DECLARE v_product_sku VARCHAR(50);
                DECLARE v_done INT DEFAULT FALSE;
                DECLARE v_order_number VARCHAR(20);
                DECLARE v_has_error INT DEFAULT FALSE;
                
                -- Cursor declaration must come after variable declarations
                DECLARE cart_cursor CURSOR FOR 
                    SELECT ci.product_id, ci.quantity, (p.base_price_cents / 100), i.stock, c.name, p.sku
                    FROM cart_items ci
                    JOIN products p ON ci.product_id = p.id
                    JOIN inventory i ON p.id = i.product_id
                    JOIN cards c ON p.card_id = c.id
                    WHERE ci.user_id = p_user_id;
                    
                -- Handlers must be declared last before the main logic
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    IF v_has_error = FALSE THEN
                        SET p_status = "ERROR";
                        SET p_message = "Order placement failed due to database error";
                        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
                    END IF;
                END;
                
                -- Input validation
                IF p_shipping_first_name IS NULL OR p_shipping_last_name IS NULL OR 
                   p_shipping_email IS NULL OR p_shipping_phone IS NULL OR
                   p_shipping_address_line_1 IS NULL OR p_shipping_city IS NULL OR 
                   p_shipping_state IS NULL OR p_shipping_postal_code IS NULL THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Missing required shipping information";
                END IF;
                
                -- Initialize status
                SET p_status = "ERROR";
                SET p_message = "";
                
                START TRANSACTION;
                
                IF (SELECT COUNT(*) FROM cart_items WHERE user_id = p_user_id) = 0 THEN
                    SET p_status = "ERROR";
                    SET p_message = "Cart is empty";
                    ROLLBACK;
                ELSE
                    OPEN cart_cursor;
                    read_loop: LOOP
                        FETCH cart_cursor INTO v_product_id, v_quantity, v_unit_price, v_stock, v_product_name, v_product_sku;
                        IF v_done THEN
                            LEAVE read_loop;
                        END IF;
                        
                        IF v_stock < v_quantity THEN
                            SET v_has_error = TRUE;
                            SET p_status = "ERROR";
                            SET p_message = CONCAT("Insufficient stock for product: ", v_product_name);
                            CLOSE cart_cursor;
                            ROLLBACK;
                            LEAVE read_loop;
                        END IF;
                        
                        SET v_subtotal = v_subtotal + (v_unit_price * v_quantity);
                    END LOOP;
                    CLOSE cart_cursor;
                    
                    -- Only continue if no error was set
                    IF v_has_error = FALSE THEN
                        SET v_tax_amount = v_subtotal * p_tax_rate;
                        SET v_total_amount = v_subtotal + v_tax_amount + p_shipping_cost;
                        SET v_order_number = CONCAT("ORD-", DATE_FORMAT(NOW(), "%Y%m%d"), LPAD(FLOOR(RAND() * 10000), 4, "0"));
                        
                        INSERT INTO orders (
                            order_number, user_id, status, payment_method, payment_status,
                            subtotal, tax_amount, shipping_cost, total_amount,
                            shipping_first_name, shipping_last_name, shipping_email, 
                            shipping_phone, shipping_address_line_1, shipping_address_line_2,
                            shipping_city, shipping_state, shipping_postal_code, shipping_country,
                            special_instructions, created_at, updated_at
                        ) VALUES (
                            v_order_number, p_user_id, "pending", p_payment_method, "pending",
                            v_subtotal, v_tax_amount, p_shipping_cost, v_total_amount,
                            p_shipping_first_name, p_shipping_last_name, p_shipping_email,
                            p_shipping_phone, p_shipping_address_line_1, p_shipping_address_line_2,
                            p_shipping_city, p_shipping_state, p_shipping_postal_code, p_shipping_country,
                            p_special_instructions, NOW(), NOW()
                        );
                        
                        SET p_order_id = LAST_INSERT_ID();
                        SET v_done = FALSE;
                        
                        OPEN cart_cursor;
                        insert_loop: LOOP
                            FETCH cart_cursor INTO v_product_id, v_quantity, v_unit_price, v_stock, v_product_name, v_product_sku;
                            IF v_done THEN
                                LEAVE insert_loop;
                            END IF;
                            
                            INSERT INTO order_items (
                                order_id, product_id, product_name, product_sku,
                                unit_price, quantity, total_price, price_in_base_currency, created_at, updated_at
                            ) VALUES (
                                p_order_id, v_product_id, v_product_name, v_product_sku,
                                v_unit_price, v_quantity, (v_unit_price * v_quantity), 
                                (v_unit_price * v_quantity * 100), NOW(), NOW()
                            );
                            
                            UPDATE inventory SET stock = stock - v_quantity WHERE product_id = v_product_id;
                        END LOOP;
                        CLOSE cart_cursor;
                        
                        DELETE FROM cart_items WHERE user_id = p_user_id;
                        
                        SET p_order_number = v_order_number;
                        SET p_total_amount = v_total_amount;
                        SET p_status = "SUCCESS";
                        SET p_message = "Order placed successfully";
                        
                        COMMIT;
                    END IF;
                END IF;
            END//
DELIMITER ;

-- sp_CancelOrder - Order cancellation with stock restoration
DELIMITER //

CREATE PROCEDURE sp_CancelOrder(
    IN p_order_id BIGINT UNSIGNED,
    IN p_user_id BIGINT UNSIGNED,
    IN p_cancel_reason TEXT,
    OUT p_status VARCHAR(20),
    OUT p_message TEXT
)
BEGIN
    DECLARE v_order_status VARCHAR(50);
    DECLARE v_order_user_id BIGINT UNSIGNED;
    DECLARE v_done INT DEFAULT FALSE;
    DECLARE v_product_id BIGINT UNSIGNED;
    DECLARE v_quantity INT;
    
    DECLARE order_items_cursor CURSOR FOR 
        SELECT product_id, quantity 
        FROM order_items 
        WHERE order_id = p_order_id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status = 'ERROR';
        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
    END;
    
    START TRANSACTION;
    
    SELECT status, user_id INTO v_order_status, v_order_user_id 
    FROM orders 
    WHERE id = p_order_id;
    
    IF v_order_user_id IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = 'Order not found';
        ROLLBACK;
    ELSEIF v_order_user_id != p_user_id THEN
        SET p_status = 'ERROR';
        SET p_message = 'Unauthorized: Order does not belong to user';
        ROLLBACK;
    ELSEIF v_order_status IN ('cancelled', 'shipped', 'delivered') THEN
        SET p_status = 'ERROR';
        SET p_message = CONCAT('Cannot cancel order with status: ', v_order_status);
        ROLLBACK;
    ELSE
        OPEN order_items_cursor;
        restore_loop: LOOP
            FETCH order_items_cursor INTO v_product_id, v_quantity;
            IF v_done THEN
                LEAVE restore_loop;
            END IF;
            
            UPDATE inventory 
            SET stock = stock + v_quantity 
            WHERE product_id = v_product_id;
        END LOOP;
        CLOSE order_items_cursor;
        
        UPDATE orders 
        SET status = 'cancelled', 
            notes = CONCAT(IFNULL(notes, ''), '\nCancelled: ', p_cancel_reason),
            updated_at = NOW()
        WHERE id = p_order_id;
        
        SET p_status = 'SUCCESS';
        SET p_message = 'Order cancelled successfully';
        
        COMMIT;
    END IF;
END//

DELIMITER ;

-- sp_UpdateProductStock - Stock management with logging
DELIMITER //

CREATE PROCEDURE sp_UpdateProductStock(
    IN p_product_id BIGINT UNSIGNED,
    IN p_new_stock INT,
    IN p_reason VARCHAR(255),
    IN p_updated_by BIGINT UNSIGNED,
    OUT p_status VARCHAR(20),
    OUT p_message TEXT
)
BEGIN
    DECLARE v_current_stock INT;
    DECLARE v_product_name VARCHAR(255);
    DECLARE v_min_stock INT DEFAULT 5;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status = 'ERROR';
        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
    END;
    
    START TRANSACTION;
    
    SELECT i.stock, c.name 
    INTO v_current_stock, v_product_name
    FROM inventory i
    JOIN products p ON i.product_id = p.id
    JOIN cards c ON p.card_id = c.id
    WHERE p.id = p_product_id;
    
    IF v_product_name IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = 'Product not found';
        ROLLBACK;
    ELSEIF p_new_stock < 0 THEN
        SET p_status = 'ERROR';
        SET p_message = 'Stock cannot be negative';
        ROLLBACK;
    ELSE
        UPDATE inventory 
        SET stock = p_new_stock, updated_at = NOW() 
        WHERE product_id = p_product_id;
        
        INSERT INTO inventory_logs (
            product_id, old_stock, new_stock, change_amount, 
            reason, updated_by, created_at
        ) VALUES (
            p_product_id, v_current_stock, p_new_stock, 
            (p_new_stock - v_current_stock), p_reason, p_updated_by, NOW()
        );
        
        SET p_status = 'SUCCESS';
        SET p_message = CONCAT('Stock updated for ', v_product_name);
        
        IF p_new_stock <= v_min_stock THEN
            SET p_message = CONCAT(p_message, ' (WARNING: Low stock threshold reached)');
        END IF;
        
        COMMIT;
    END IF;
END//

DELIMITER ;

-- sp_GetUserOrderHistory - User order retrieval with pagination
DELIMITER //

CREATE PROCEDURE sp_GetUserOrderHistory(
    IN p_user_id BIGINT UNSIGNED,
    IN p_limit INT,
    IN p_offset INT,
    IN p_status VARCHAR(50)
)
BEGIN
    SET p_limit = IFNULL(p_limit, 50);
    SET p_offset = IFNULL(p_offset, 0);
    
    SELECT 
        o.id,
        o.order_number,
        o.status,
        o.payment_method,
        o.payment_status,
        o.total_amount,
        o.created_at,
        o.updated_at,
        COUNT(oi.id) as item_count,
        SUM(oi.quantity) as total_quantity
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = p_user_id
    AND (p_status IS NULL OR o.status = p_status)
    GROUP BY o.id, o.order_number, o.status, o.payment_method, 
             o.payment_status, o.total_amount, o.created_at, o.updated_at
    ORDER BY o.created_at DESC
    LIMIT p_limit OFFSET p_offset;
    
    SELECT COUNT(*) as total_orders
    FROM orders 
    WHERE user_id = p_user_id
    AND (p_status IS NULL OR status = p_status);
END//

DELIMITER ;

-- sp_ProcessRefund - Refund processing with inventory restoration
DELIMITER //

CREATE PROCEDURE sp_ProcessRefund(
    IN p_order_id BIGINT UNSIGNED,
    IN p_refund_amount DECIMAL(10,2),
    IN p_refund_reason TEXT,
    IN p_processed_by BIGINT UNSIGNED,
    OUT p_status VARCHAR(20),
    OUT p_message TEXT
)
BEGIN
    DECLARE v_order_total DECIMAL(10,2);
    DECLARE v_order_status VARCHAR(50);
    DECLARE v_done INT DEFAULT FALSE;
    DECLARE v_product_id BIGINT UNSIGNED;
    DECLARE v_quantity INT;
    
    DECLARE order_items_cursor CURSOR FOR 
        SELECT product_id, quantity 
        FROM order_items 
        WHERE order_id = p_order_id;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_status = 'ERROR';
        GET DIAGNOSTICS CONDITION 1 p_message = MESSAGE_TEXT;
    END;
    
    START TRANSACTION;
    
    SELECT total_amount, status 
    INTO v_order_total, v_order_status
    FROM orders 
    WHERE id = p_order_id;
    
    IF v_order_total IS NULL THEN
        SET p_status = 'ERROR';
        SET p_message = 'Order not found';
        ROLLBACK;
    ELSEIF v_order_status = 'refunded' THEN
        SET p_status = 'ERROR';
        SET p_message = 'Order already refunded';
        ROLLBACK;
    ELSEIF p_refund_amount > v_order_total THEN
        SET p_status = 'ERROR';
        SET p_message = 'Refund amount cannot exceed order total';
        ROLLBACK;
    ELSE
        IF p_refund_amount = v_order_total THEN
            OPEN order_items_cursor;
            refund_loop: LOOP
                FETCH order_items_cursor INTO v_product_id, v_quantity;
                IF v_done THEN
                    LEAVE refund_loop;
                END IF;
                
                UPDATE inventory 
                SET stock = stock + v_quantity 
                WHERE product_id = v_product_id;
            END LOOP;
            CLOSE order_items_cursor;
        END IF;
        
        UPDATE orders 
        SET status = IF(p_refund_amount = v_order_total, 'refunded', 'processing'),
            payment_status = 'refunded',
            notes = CONCAT(IFNULL(notes, ''), '\nRefund processed: $', p_refund_amount, ' - ', p_refund_reason),
            updated_at = NOW()
        WHERE id = p_order_id;
        
        INSERT INTO refunds (
            order_id, amount, reason, processed_by, created_at
        ) VALUES (
            p_order_id, p_refund_amount, p_refund_reason, p_processed_by, NOW()
        );
        
        SET p_status = 'SUCCESS';
        SET p_message = CONCAT('Refund of $', p_refund_amount, ' processed successfully');
        
        COMMIT;
    END IF;
END//

DELIMITER ;

-- sp_GetLowStockProducts - Low stock monitoring
DELIMITER //

CREATE PROCEDURE sp_GetLowStockProducts(
                IN p_threshold INT
            )
            BEGIN
                SET p_threshold = IFNULL(p_threshold, 5);
                SELECT 
                    p.id,
                    c.name as card_name,
                    p.sku,
                    p.condition,
                    s.name as set_name,
                    r.name as rarity_name,
                    i.stock,
                    (p.base_price_cents / 100) as price
                FROM products p
                JOIN inventory i ON p.id = i.product_id
                JOIN cards c ON p.card_id = c.id
                JOIN sets s ON c.set_id = s.id
                JOIN rarities r ON c.rarity_id = r.id
                WHERE i.stock <= p_threshold
                ORDER BY i.stock ASC, c.name ASC;
            END//
DELIMITER ;

-- sp_ArchiveOldOrders - Archive old orders for performance
DELIMITER //

 CREATE PROCEDURE sp_ArchiveOldOrders(
                IN p_days_old INT,
                OUT p_archived_count INT
            )
            BEGIN
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_archived_count = -1;
                    RESIGNAL;
                END;

                SET p_days_old = IFNULL(p_days_old, 365);
                SET p_archived_count = 0;
                
                START TRANSACTION;
                
                -- First, archive the main orders
                INSERT INTO archived_orders 
                SELECT *, NOW() as archived_at 
                FROM orders 
                WHERE status IN ("delivered", "cancelled", "refunded")
                AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                SET p_archived_count = ROW_COUNT();
                
                -- Archive order items
                INSERT INTO archived_order_items
                SELECT oi.*, NOW() as archived_at
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                -- Archive inventory adjustments (if any exist for these orders)
                INSERT INTO archived_inventory_adjustments
                SELECT ia.*, NOW() as archived_at
                FROM inventory_adjustments ia
                JOIN orders o ON ia.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                -- Now delete in proper order (children first, then parents)
                
                -- Delete inventory adjustments first (they reference orders)
                DELETE ia FROM inventory_adjustments ia
                JOIN orders o ON ia.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                -- Delete order items
                DELETE oi FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status IN ("delivered", "cancelled", "refunded")
                AND o.created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                -- Finally delete orders (no more FK references)
                DELETE FROM orders 
                WHERE status IN ("delivered", "cancelled", "refunded")
                AND created_at < DATE_SUB(NOW(), INTERVAL p_days_old DAY);
                
                COMMIT;
            END//

DELIMITER ;

-- =============================================================================
-- 9. DATABASE TRIGGERS
-- =============================================================================

DELIMITER //

-- tr_orders_inventory_update - Automatic Inventory Management
CREATE TRIGGER tr_orders_inventory_update
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    -- If order status changes to "shipped", log the shipment
    IF OLD.status != NEW.status AND NEW.status = 'shipped' THEN
        INSERT INTO order_status_log (
            order_id, old_status, new_status, changed_by, 
            change_reason, created_at
        ) VALUES (
            NEW.id, OLD.status, NEW.status, IFNULL(@current_user_id, 1),
            'Order shipped', NOW()
        );
    END IF;
    
    -- If order is cancelled, create inventory adjustment record
    IF OLD.status != NEW.status AND NEW.status = 'cancelled' THEN
        INSERT INTO inventory_adjustments (
            order_id, adjustment_type, reason, created_at
        ) VALUES (
            NEW.id, 'restore', 'Order cancelled', NOW()
        );
    END IF;
END//

DELIMITER ;

-- tr_products_price_history - Price Change Tracking
DELIMITER //

CREATE TRIGGER tr_products_price_history
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    -- Log price changes (convert cents to decimal for comparison)
    IF OLD.base_price_cents != NEW.base_price_cents THEN
        INSERT INTO price_history (
            product_id, old_price, new_price, change_amount,
            change_percentage, changed_by, created_at
        ) VALUES (
            NEW.id, (OLD.base_price_cents / 100), (NEW.base_price_cents / 100), 
            ((NEW.base_price_cents - OLD.base_price_cents) / 100),
            ROUND(((NEW.base_price_cents - OLD.base_price_cents) / OLD.base_price_cents) * 100, 2),
           IFNULL(@current_user_id, 1), NOW()
        );
    END IF;
END//

DELIMITER ;

-- tr_cart_items_validation - Cart Item Validation
DELIMITER //

CREATE TRIGGER tr_cart_items_validation
BEFORE INSERT ON cart_items
FOR EACH ROW
BEGIN
    DECLARE v_stock INT;
    DECLARE v_product_exists INT DEFAULT 0;
    DECLARE v_error_msg TEXT;
    
    -- Validate product exists and get stock
    SELECT COUNT(*) INTO v_product_exists
    FROM products p
    JOIN inventory i ON p.id = i.product_id
    WHERE p.id = NEW.product_id;
    
    IF v_product_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Product not found';
    END IF;
    
    -- Get stock separately
    SELECT i.stock INTO v_stock
    FROM inventory i
    WHERE i.product_id = NEW.product_id;
    
    IF NEW.quantity <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quantity must be greater than 0';
    END IF;
    
    IF NEW.quantity > v_stock THEN
        SET v_error_msg = CONCAT('Insufficient stock. Available: ', v_stock, ', Requested: ', NEW.quantity);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_msg;
    END IF;
    
    -- Set timestamps
    SET NEW.created_at = NOW();
    SET NEW.updated_at = NOW();
END//

DELIMITER ;

-- tr_inventory_low_stock_alert - Stock Threshold Monitoring
DELIMITER //

CREATE TRIGGER tr_inventory_low_stock_alert
AFTER UPDATE ON inventory
FOR EACH ROW
BEGIN
    DECLARE v_product_name VARCHAR(255);
    DECLARE v_threshold INT DEFAULT 5;
    
    -- Get product name from cards table
    SELECT c.name INTO v_product_name 
    FROM products p
    JOIN cards c ON p.card_id = c.id
    WHERE p.id = NEW.product_id;
    
    -- Check if stock dropped below threshold
    IF OLD.stock > v_threshold AND NEW.stock <= v_threshold THEN
        INSERT INTO stock_alerts (
            product_id, product_name, current_stock, threshold_value,
            alert_type, message, created_at
        ) VALUES (
            NEW.product_id, v_product_name, NEW.stock, v_threshold,
            'low_stock', 
            CONCAT('Product "', v_product_name, '" is below stock threshold. Current stock: ', CAST(NEW.stock AS CHAR)),
            NOW()
        );
    END IF;
    
    -- Check if stock reaches zero
    IF OLD.stock > 0 AND NEW.stock = 0 THEN
        INSERT INTO stock_alerts (
            product_id, product_name, current_stock, threshold_value,
            alert_type, message, created_at
        ) VALUES (
            NEW.product_id, v_product_name, NEW.stock, 0,
            'out_of_stock', 
            CONCAT('Product "', v_product_name, '" is now out of stock'),
            NOW()
        );
    END IF;
END//

DELIMITER ;

-- tr_user_activity_log - User Action Tracking
DELIMITER //

CREATE TRIGGER tr_user_activity_log_insert
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    INSERT INTO user_activity_log (
        user_id, activity_type, description, 
        related_table, related_id, created_at
    ) VALUES (
        NEW.user_id, 'order_placed', 
        CONCAT('Order placed: ', NEW.order_number, ' - Total: $', CAST(NEW.total_amount AS CHAR)),
        'orders', NEW.id, NOW()
    );
END//

DELIMITER ;

-- tr_order_total_calculation - Automatic Order Total Updates
DELIMITER //

CREATE TRIGGER tr_order_total_recalculation
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    DECLARE v_new_subtotal DECIMAL(10,2);
    DECLARE v_tax_rate DECIMAL(5,4) DEFAULT 0.08;
    DECLARE v_shipping_cost DECIMAL(10,2);
    DECLARE v_tax_amount DECIMAL(10,2);
    DECLARE v_total_amount DECIMAL(10,2);
    
    -- Recalculate order totals
    SELECT SUM(total_price), o.shipping_cost
    INTO v_new_subtotal, v_shipping_cost
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.order_id = NEW.order_id
    GROUP BY o.shipping_cost;
    
    SET v_tax_amount = v_new_subtotal * v_tax_rate;
    SET v_total_amount = v_new_subtotal + v_tax_amount + v_shipping_cost;
    
    -- Update order totals
    UPDATE orders 
    SET subtotal = v_new_subtotal,
        tax_amount = v_tax_amount,
        total_amount = v_total_amount,
        updated_at = NOW()
    WHERE id = NEW.order_id;
END//

DELIMITER ;

-- tr_product_search_index - Search Index Maintenance
DELIMITER //
CREATE TRIGGER tr_product_search_index_update
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    -- Update search index when product details change
    IF OLD.base_price_cents != NEW.base_price_cents OR OLD.`condition` != NEW.`condition` THEN
        INSERT INTO search_index_updates (
                table_name, record_id, action, created_at
            ) VALUES (
                "products", NEW.id, "update", NOW()
            );
    END IF;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER tr_product_search_index_insert
AFTER INSERT ON products
FOR EACH ROW
BEGIN
    INSERT INTO search_index_updates (
        table_name, record_id, action, created_at
    ) VALUES (
        'products', NEW.id, 'insert', NOW()
    );
END//
DELIMITER ;

-- tr_cart_items_update_validation - Cart Update Validation
DELIMITER //

CREATE TRIGGER tr_cart_items_update_validation
BEFORE UPDATE ON cart_items
FOR EACH ROW
BEGIN
    DECLARE v_stock INT;
    DECLARE v_product_exists INT DEFAULT 0;
    DECLARE v_error_msg TEXT;
    
    -- Validate product still exists
    SELECT COUNT(*) INTO v_product_exists
    FROM products p
    JOIN inventory i ON p.id = i.product_id
    WHERE p.id = NEW.product_id;
    
    IF v_product_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Product not found';
    END IF;
    
    -- Get stock separately
    SELECT i.stock INTO v_stock
    FROM inventory i
    WHERE i.product_id = NEW.product_id;
    
    IF NEW.quantity <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Quantity must be greater than 0';
    END IF;
    
    IF NEW.quantity > v_stock THEN
        SET v_error_msg = CONCAT('Insufficient stock. Available: ', v_stock, ', Requested: ', NEW.quantity);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error_msg;
    END IF;
    
    -- Update timestamp
    SET NEW.updated_at = NOW();
END//

DELIMITER ;

-- =============================================================================
-- 10. MYSQL ROLES
-- =============================================================================

-- Create MySQL roles for database-layer security
CREATE ROLE IF NOT EXISTS 'konibui_admin';
CREATE ROLE IF NOT EXISTS 'konibui_employee'; 
CREATE ROLE IF NOT EXISTS 'konibui_customer';

-- Admin role privileges (full access)
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.users TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.roles TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.role_user TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_admin';
GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache_locks TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.jobs TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.job_batches TO 'konibui_admin';
GRANT SELECT ON konibui.failed_jobs TO 'konibui_admin';
GRANT SELECT ON konibui.migrations TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.products TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.categories TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.orders TO 'konibui_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.order_items TO 'konibui_admin';

-- Employee role privileges (limited staff operations)
GRANT SELECT, INSERT, UPDATE ON konibui.users TO 'konibui_employee';
GRANT SELECT ON konibui.roles TO 'konibui_employee';
GRANT SELECT ON konibui.role_user TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_employee';
GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.cache TO 'konibui_employee';
GRANT SELECT ON konibui.failed_jobs TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.jobs TO 'konibui_employee';
GRANT SELECT ON konibui.migrations TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE ON konibui.products TO 'konibui_employee';
GRANT SELECT ON konibui.categories TO 'konibui_employee';
GRANT SELECT, UPDATE ON konibui.orders TO 'konibui_employee';
GRANT SELECT, INSERT, UPDATE ON konibui.order_items TO 'konibui_employee';

-- Customer role privileges (customer-facing operations only)
GRANT SELECT, UPDATE ON konibui.users TO 'konibui_customer';
GRANT SELECT ON konibui.roles TO 'konibui_customer';
GRANT SELECT, INSERT, UPDATE, DELETE ON konibui.sessions TO 'konibui_customer';
GRANT SELECT, INSERT, DELETE ON konibui.password_reset_tokens TO 'konibui_customer';
GRANT SELECT ON konibui.products TO 'konibui_customer';
GRANT SELECT ON konibui.categories TO 'konibui_customer';
GRANT SELECT, INSERT ON konibui.orders TO 'konibui_customer';
GRANT SELECT, INSERT ON konibui.order_items TO 'konibui_customer';

FLUSH PRIVILEGES;

-- =============================================================================
-- END OF SCHEMA
-- =============================================================================