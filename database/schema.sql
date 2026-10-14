

CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NAME       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    PASSWORD   VARCHAR(255) NOT NULL,              -- bcrypt hash from password_hash()
    ROLE       ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tasks (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    title       VARCHAR(150) NOT NULL,
    DESCRIPTION TEXT NULL,
    STATUS      ENUM('pending','done') NOT NULL DEFAULT 'pending',
    due_date    DATE NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4;

-- Default accounts for grading
-- Admin: admin@example.com / Admin@123
-- User : user@example.com  / User@1234
INSERT INTO users (NAME, email, PASSWORD, ROLE) VALUES
('Administrator', 'admin@example.com', '$2y$12$oA3p550AbqQ.kV2fLY9dCOI6YWi8RvfPAqvrX1pPJaGl7RS.ijGmu', 'admin'),
('Demo User',     'user@example.com',  '$2y$12$r0Z8kqbsAH7XttrPtgd9TeUcAHTCVAXQBZK1HS1d5Ai9XtGACyx02', 'user');

INSERT INTO tasks (user_id, title, DESCRIPTION, STATUS, due_date) VALUES
(2, 'Finish final project', 'Deploy the app to free hosting.', 'pending', DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
(2, 'Push code to GitHub', 'Include database/schema.sql and README.', 'done', NULL);
