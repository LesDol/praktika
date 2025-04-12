-- Create database
CREATE DATABASE IF NOT EXISTS college_videos;
USE college_videos;

-- Create colleges table
CREATE TABLE IF NOT EXISTS colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    website VARCHAR(255),
    logo_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'college') NOT NULL DEFAULT 'college',
    college_id INT,
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_token_expiry DATETIME DEFAULT NULL,
    last_login DATETIME DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE SET NULL
);

-- Create videos table
CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    url VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    file_type VARCHAR(50) DEFAULT NULL,
    thumbnail_url VARCHAR(255),
    duration VARCHAR(10),
    views INT DEFAULT 0,
    status ENUM('pending', 'published', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE CASCADE
);

-- Create video_tags table
CREATE TABLE IF NOT EXISTS video_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
);

-- Create comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    user_id INT NOT NULL,
    text TEXT NOT NULL,
    status ENUM('active', 'hidden') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create likes table
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY video_user (video_id, user_id),
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create audit_log table
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert admin user
INSERT INTO users (email, password, role) VALUES 
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample colleges
INSERT INTO colleges (name, description) VALUES
('Технический колледж', 'Колледж технического профиля с программами обучения в области IT, электроники и машиностроения'),
('Медицинский колледж', 'Подготовка специалистов в области медицины и здравоохранения'),
('Педагогический колледж', 'Обучение будущих педагогов и преподавателей');

-- Create indexes
CREATE INDEX idx_videos_college ON videos(college_id);
CREATE INDEX idx_videos_status ON videos(status);
CREATE INDEX idx_comments_video ON comments(video_id);
CREATE INDEX idx_video_tags_video ON video_tags(video_id);
CREATE INDEX idx_video_tags_name ON video_tags(name);

-- Очищаем таблицы
DELETE FROM video_tags;
DELETE FROM comments;
DELETE FROM videos;
DELETE FROM users WHERE role != 'admin';

-- Добавляем тестового пользователя
INSERT INTO users (email, password, role, status) VALUES 
('test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'college', 'active');

-- Добавляем видео для Технического колледжа
INSERT INTO videos (college_id, title, description, url, duration, views, status) VALUES
(1, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', '00:04:00', 3900, 'published'),
(1, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', '00:04:00', 3900, 'published');

-- Добавляем видео для Медицинского колледжа
INSERT INTO videos (college_id, title, description, url, duration, views, status) VALUES
(2, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', '00:04:00', 3900, 'published'),
(2, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', '00:04:00', 3900, 'published');

-- Добавляем видео для Педагогического колледжа
INSERT INTO videos (college_id, title, description, url, duration, views, status) VALUES
(3, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', '00:04:00', 3900, 'published'),
(3, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', '00:04:00', 3900, 'published');

-- Добавляем теги для всех видео
INSERT INTO video_tags (video_id, name) VALUES
(1, 'программирование'), (1, 'python'), (1, 'веб-разработка'), (1, 'обучение'),
(2, 'программирование'), (2, 'python'), (2, 'веб-разработка'), (2, 'обучение'),
(3, 'программирование'), (3, 'python'), (3, 'веб-разработка'), (3, 'обучение'),
(4, 'программирование'), (4, 'python'), (4, 'веб-разработка'), (4, 'обучение'),
(5, 'программирование'), (5, 'python'), (5, 'веб-разработка'), (5, 'обучение'),
(6, 'программирование'), (6, 'python'), (6, 'веб-разработка'), (6, 'обучение');

-- Добавляем комментарии к видео от тестового пользователя
INSERT INTO comments (video_id, user_id, text, status) VALUES
(1, 2, 'Отличный курс! Очень помог разобраться с созданием сайтов на Python.', 'active'),
(1, 2, 'Спасибо за подробное объяснение работы с Flask.', 'active'),
(2, 2, 'Отличный материал для начинающих веб-разработчиков на Python!', 'active'),
(2, 2, 'Спасибо за подробное объяснение работы с маршрутами.', 'active');

-- Обновляем превьюшки для всех видео
UPDATE videos SET thumbnail_url = 'https://rutube.ru/api/video/ac187cc09d664dbbcf6f6c508b10eb4f/thumbnail/';

-- Добавляем новые колонки для локальных видео
ALTER TABLE videos 
ADD COLUMN file_path VARCHAR(255) DEFAULT NULL AFTER url,
ADD COLUMN file_type VARCHAR(50) DEFAULT NULL AFTER file_path; 