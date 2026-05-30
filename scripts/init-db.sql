SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS canteen_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE canteen_db;

CREATE TABLE IF NOT EXISTS admin_user (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','kitchen') NOT NULL DEFAULT 'kitchen',
<<<<<<< ours
<<<<<<< ours
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    userid INT UNSIGNED,
=======
=======
>>>>>>> theirs
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    userid INT UNSIGNED DEFAULT NULL,
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
    username VARCHAR(64) NULL,
    name VARCHAR(100) NULL,
    lastname VARCHAR(100) NULL,
    email VARCHAR(120) NULL,
<<<<<<< ours
<<<<<<< ours
    group_id VARCHAR(100) NULL,
    cardnumber VARCHAR(64) NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
=======
=======
>>>>>>> theirs
    project VARCHAR(200) NULL,
    cardnumber VARCHAR(64) NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
    UNIQUE KEY uniq_id (userid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS meal_selection (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
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
    UNIQUE KEY uniq_sheet_row (sheet_row_id),
    KEY idx_staff (staff_id),
    KEY idx_imported (imported_at),
    KEY idx_week (week_start_date),
    CONSTRAINT fk_meal_user FOREIGN KEY (staff_id) REFERENCES user(userid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS serving_queue (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    staff_id INT UNSIGNED NOT NULL,
    served_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shift_type ENUM('Day','Night') NOT NULL,
    meal_label VARCHAR(100) NOT NULL,
    diet_notes VARCHAR(255) NULL,
<<<<<<< ours
<<<<<<< ours
=======
    receipt_status VARCHAR(64) NOT NULL DEFAULT 'Receipt not printed',
>>>>>>> theirs
=======
    receipt_status VARCHAR(64) NOT NULL DEFAULT 'Receipt not printed',
>>>>>>> theirs
    served_by INT UNSIGNED NULL,
    CONSTRAINT fk_queue_user FOREIGN KEY (staff_id) REFERENCES user(userid) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_queue_admin FOREIGN KEY (served_by) REFERENCES admin_user(id) ON DELETE SET NULL ON UPDATE CASCADE,
    KEY idx_served_at (served_at)
<<<<<<< ours
<<<<<<< ours
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
=======
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
>>>>>>> theirs
=======
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
>>>>>>> theirs
