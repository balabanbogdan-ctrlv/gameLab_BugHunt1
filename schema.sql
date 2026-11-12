-- GameLab Bug Hunt. Importați într-o instanță LOCALĂ MySQL/MariaDB.
CREATE DATABASE IF NOT EXISTS gamelab_bug_hunt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamelab_bug_hunt;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS games (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    genre VARCHAR(40) NOT NULL,
    platform VARCHAR(40) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    release_date DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_games_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_games_user_created (user_id, created_at, id),
    INDEX idx_games_user_genre (user_id, genre)
) ENGINE=InnoDB;

-- Cont de demonstrație (doar laborator local): demo / Demo2026!
INSERT INTO users (username, password_hash)
VALUES ('demo', '$2y$12$vkIkoEYERyVi1E1qLPCpA.ilDfFyCok.cVwn.OWUH5ktzMgekCtsq')
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id);
SET @demo_id = (SELECT id FROM users WHERE username = 'demo');

-- Jocuri fictive pentru testarea căutării, filtrelor, prețurilor și stocurilor.
-- Nu dublați înregistrările dacă schema este importată încă o dată.
INSERT INTO games (user_id, title, genre, platform, price, stock, release_date)
SELECT @demo_id, sample.title, sample.genre, sample.platform, sample.price, sample.stock, sample.release_date
FROM (
    SELECT 'Insula Codurilor' AS title, 'Aventură' AS genre, 'PC' AS platform, 149.99 AS price, 7 AS stock, '2024-03-14' AS release_date
    UNION ALL SELECT 'Circuit Neon' AS title, 'Acțiune' AS genre, 'PlayStation' AS platform, 249.50 AS price, 0 AS stock, '2025-01-08' AS release_date
    UNION ALL SELECT 'Regatul Pixelilor' AS title, 'RPG' AS genre, 'PC' AS platform, 319.99 AS price, 12 AS stock, '2023-11-20' AS release_date
    UNION ALL SELECT 'Cursa Galactică' AS title, 'Sport' AS genre, 'Xbox' AS platform, 199.00 AS price, 3 AS stock, '2025-05-06' AS release_date
    UNION ALL SELECT 'Arhitecții Viitorului' AS title, 'Simulare' AS genre, 'PC' AS platform, 89.75 AS price, 8 AS stock, '2022-09-02' AS release_date
    UNION ALL SELECT 'Șahul Stelelor' AS title, 'Strategie' AS genre, 'Nintendo Switch' AS platform, 129.90 AS price, 5 AS stock, '2021-04-28' AS release_date
    UNION ALL SELECT 'Labirintul Quantum' AS title, 'Puzzle' AS genre, 'PC' AS platform, 59.99 AS price, 0 AS stock, '2025-07-13' AS release_date
    UNION ALL SELECT 'Expediția Orion' AS title, 'Aventură' AS genre, 'Xbox' AS platform, 279.99 AS price, 6 AS stock, '2024-12-01' AS release_date
    UNION ALL SELECT 'Arena Robotilor' AS title, 'Acțiune' AS genre, 'PC' AS platform, 179.00 AS price, 4 AS stock, '2023-08-19' AS release_date
    UNION ALL SELECT 'Legenda Meridianului' AS title, 'RPG' AS genre, 'PlayStation' AS platform, 349.99 AS price, 2 AS stock, '2025-09-24' AS release_date
    UNION ALL SELECT 'Strategii Maritime' AS title, 'Strategie' AS genre, 'PC' AS platform, 119.99 AS price, 9 AS stock, '2024-06-18' AS release_date
    UNION ALL SELECT 'Festivalul Sportiv' AS title, 'Sport' AS genre, 'Nintendo Switch' AS platform, 229.50 AS price, 0 AS stock, '2022-10-11' AS release_date
) AS sample
WHERE NOT EXISTS (SELECT 1 FROM games WHERE user_id = @demo_id);
