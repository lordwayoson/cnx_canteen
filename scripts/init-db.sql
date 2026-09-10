SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS canteen_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE canteen_db;

CREATE TABLE IF NOT EXISTS admin_user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(64) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','kitchen') NOT NULL DEFAULT 'kitchen',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    userid INT UNSIGNED DEFAULT NULL,
    username VARCHAR(64) NULL,
    name VARCHAR(100) NULL,
    lastname VARCHAR(100) NULL,
    email VARCHAR(120) NULL,
    project VARCHAR(200) NULL,
    cardnumber VARCHAR(64) NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_userid (userid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS meal_selection (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    staff_id INT UNSIGNED NOT NULL,
    project VARCHAR(150) NOT NULL,
    shift_type ENUM('Day','Night') NOT NULL,
    mon VARCHAR(100) NULL,
    tue VARCHAR(100) NULL,
    wed VARCHAR(100) NULL,
    thu VARCHAR(100) NULL,
    fri VARCHAR(100) NULL,
    sat VARCHAR(100) NULL,
    sun VARCHAR(100) NULL,
    diet_notes VARCHAR(255) NULL,
    sheet_row_id VARCHAR(191) NOT NULL,
    imported_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    week_start_date DATE NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_sheet_row (sheet_row_id),
    KEY idx_staff (staff_id),
    KEY idx_imported (imported_at),
    KEY idx_week (week_start_date),
    CONSTRAINT fk_meal_user FOREIGN KEY (staff_id) REFERENCES user(userid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS serving_queue (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    staff_id INT UNSIGNED NOT NULL,
    served_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shift_type ENUM('Day','Night') NOT NULL,
    meal_label VARCHAR(100) NOT NULL,
    diet_notes VARCHAR(255) NULL,
    receipt_status VARCHAR(64) NOT NULL DEFAULT 'Receipt not printed',
    served_by INT UNSIGNED NULL,
    PRIMARY KEY (id),
    KEY idx_served_at (served_at),
    CONSTRAINT fk_queue_user FOREIGN KEY (staff_id) REFERENCES user(userid) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_queue_admin FOREIGN KEY (served_by) REFERENCES admin_user(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
