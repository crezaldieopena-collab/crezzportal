CREATE DATABASE IF NOT EXISTS login2_db;
USE login2_db;

CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    last_name VARCHAR(100) CHARACTER SET utf8mb4 NULL,
    first_name VARCHAR(100) CHARACTER SET utf8mb4 NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

SET @add_last_name = IF(
    EXISTS (SELECT 1 FROM information_schema.columns
            WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'last_name'),
    'DO 0',
    'ALTER TABLE users ADD COLUMN last_name VARCHAR(100) CHARACTER SET utf8mb4 NULL'
);
PREPARE migration FROM @add_last_name;
EXECUTE migration;
DEALLOCATE PREPARE migration;

SET @add_first_name = IF(
    EXISTS (SELECT 1 FROM information_schema.columns
            WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'first_name'),
    'DO 0',
    'ALTER TABLE users ADD COLUMN first_name VARCHAR(100) CHARACTER SET utf8mb4 NULL'
);
PREPARE migration FROM @add_first_name;
EXECUTE migration;
DEALLOCATE PREPARE migration;

INSERT INTO users (username, password)
VALUES ('admin', MD5('admin123'))
ON DUPLICATE KEY UPDATE password = MD5('admin123');