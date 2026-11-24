-- =====================================================================
-- Connekt India: database schema (MySQL 5.7+ / MariaDB 10.3+)
-- Import in hPanel → Databases → phpMyAdmin → select your DB → Import
-- =====================================================================
SET NAMES utf8mb4;
SET time_zone = '+05:30';

CREATE TABLE IF NOT EXISTS `enquiries` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)  NOT NULL,
  `email`       VARCHAR(150)  NOT NULL,
  `phone`       VARCHAR(20)   NOT NULL,
  `company`     VARCHAR(150)  DEFAULT NULL,
  `service`     VARCHAR(80)   NOT NULL,
  `budget`      VARCHAR(60)   DEFAULT NULL,
  `message`     TEXT          NOT NULL,
  `status`      ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  `ip`          VARCHAR(45)   DEFAULT NULL,
  `user_agent`  VARCHAR(255)  DEFAULT NULL,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `careers` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`            VARCHAR(100) NOT NULL,
  `email`           VARCHAR(150) NOT NULL,
  `phone`           VARCHAR(20)  NOT NULL,
  `position`        VARCHAR(80)  NOT NULL,
  `experience`      VARCHAR(40)  DEFAULT NULL,
  `portfolio`       VARCHAR(255) DEFAULT NULL,
  `resume_path`     VARCHAR(255) NOT NULL,
  `resume_original` VARCHAR(150) DEFAULT NULL,
  `message`         TEXT         DEFAULT NULL,
  `status`          ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  `ip`              VARCHAR(45)  DEFAULT NULL,
  `user_agent`      VARCHAR(255) DEFAULT NULL,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `newsletter` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`       VARCHAR(150) NOT NULL,
  `status`      ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  `ip`          VARCHAR(45)  DEFAULT NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `chatbot_leads` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `phone`       VARCHAR(20)  NOT NULL,
  `question`    VARCHAR(500) DEFAULT NULL,
  `page_url`    VARCHAR(255) DEFAULT NULL,
  `status`      ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  `ip`          VARCHAR(45)  DEFAULT NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admins` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`        VARCHAR(60)  NOT NULL,
  `email`           VARCHAR(150) DEFAULT NULL,
  `password_hash`   VARCHAR(255) NOT NULL,
  `failed_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until`    DATETIME     DEFAULT NULL,
  `last_login`      DATETIME     DEFAULT NULL,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Used by the API rate limiter (5 submissions / IP / 10 minutes) and login throttling
CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip`          VARCHAR(45) NOT NULL,
  `action`      VARCHAR(30) NOT NULL,
  `created_at`  DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lookup` (`ip`, `action`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The first admin user is created by visiting /admin/setup.php (then delete that file).
