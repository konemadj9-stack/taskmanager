CREATE DATABASE IF NOT EXISTS taskmanager
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE taskmanager;

CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    status ENUM('todo', 'done') NOT NULL DEFAULT 'todo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
