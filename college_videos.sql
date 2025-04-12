-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 12 2025 г., 11:23
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `college_videos`
--

-- --------------------------------------------------------

--
-- Структура таблицы `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, 'registration', 'colleges', 4, 'Регистрация нового колледжа: 222', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:137.0) Gecko/20100101 Firefox/137.0', '2025-04-12 09:23:06');

-- --------------------------------------------------------

--
-- Структура таблицы `colleges`
--

CREATE TABLE `colleges` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `colleges`
--

INSERT INTO `colleges` (`id`, `name`, `description`, `location`, `website`, `logo_url`, `created_at`, `updated_at`) VALUES
(1, 'Технический колледж', 'Колледж технического профиля с программами обучения в области IT, электроники и машиностроения', NULL, NULL, NULL, '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(2, 'Медицинский колледж', 'Подготовка специалистов в области медицины и здравоохранения', NULL, NULL, NULL, '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(3, 'Педагогический колледж', 'Обучение будущих педагогов и преподавателей', NULL, NULL, NULL, '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(4, '222', NULL, NULL, NULL, NULL, '2025-04-12 09:23:06', '2025-04-12 09:23:06');

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `status` enum('active','hidden') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `video_id`, `user_id`, `text`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Отличный курс! Очень помог разобраться с созданием сайтов на Python.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(2, 1, 1, 'Спасибо за подробное объяснение работы с Flask.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(3, 1, 1, 'Было бы здорово добавить больше практических примеров.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(4, 1, 1, 'Очень понятное объяснение создания HTML шаблонов.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(5, 2, 1, 'Отличный материал для начинающих веб-разработчиков на Python!', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(6, 2, 1, 'Спасибо за подробное объяснение работы с маршрутами.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(7, 2, 1, 'Очень полезная информация про создание веб-приложений.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(8, 2, 1, 'Было бы здорово добавить раздел про базы данных.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(9, 3, 1, 'Очень важная информация для начинающих программистов!', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(10, 3, 1, 'Спасибо за подробное объяснение работы с формами.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(11, 3, 1, 'Отличные практические примеры создания сайтов.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(12, 3, 1, 'Было бы здорово добавить больше информации про развертывание.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(13, 4, 1, 'Очень интересная лекция! Особенно понравился раздел про шаблоны.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(14, 4, 1, 'Спасибо за подробное объяснение работы с Flask.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(15, 4, 1, 'Отличные примеры создания веб-приложений.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(16, 4, 1, 'Было бы здорово добавить больше информации про аутентификацию.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(17, 5, 1, 'Очень полезная информация для начинающих разработчиков!', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(18, 5, 1, 'Спасибо за практические советы по созданию сайтов.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(19, 5, 1, 'Отличные примеры работы с Flask.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(20, 5, 1, 'Было бы здорово добавить больше информации про API.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(21, 6, 1, 'Очень важная тема! Спасибо за подробное освещение.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(22, 6, 1, 'Отличные практические советы по веб-разработке.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(23, 6, 1, 'Очень полезная информация про создание сайтов.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(24, 6, 1, 'Было бы здорово добавить больше примеров из практики.', 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(26, 1, 2, 'Чел мега плох', 'active', '2025-04-12 09:23:34', '2025-04-12 09:23:34');

-- --------------------------------------------------------

--
-- Структура таблицы `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','college') NOT NULL DEFAULT 'college',
  `college_id` int(11) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `college_id`, `reset_token`, `reset_token_expiry`, `last_login`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, NULL, NULL, 'active', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(2, 'example@mail.com', '$2y$10$e7EHqdN83bOrLfYojJGPEuVszUVrCBFTf8r4atruEnMV88g1outRS', 'college', 4, NULL, NULL, '2025-04-12 12:23:12', 'active', '2025-04-12 09:23:06', '2025-04-12 09:23:12');

-- --------------------------------------------------------

--
-- Структура таблицы `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `status` enum('pending','published','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `videos`
--

INSERT INTO `videos` (`id`, `college_id`, `title`, `description`, `url`, `file_path`, `file_type`, `thumbnail_url`, `duration`, `views`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', NULL, NULL, 'https://rutube.ru/api/video/ac187cc09d664dbbcf6f6c508b10eb4f/thumbnail/', '00:04:00', 3903, 'published', '2025-04-12 09:18:56', '2025-04-12 09:23:34'),
(2, 1, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', NULL, NULL, 'https://rutube.ru/api/video/ac187cc09d664dbbcf6f6c508b10eb4f/thumbnail/', '00:04:00', 3900, 'published', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(3, 2, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', NULL, NULL, 'https://rutube.ru/api/video/ac187cc09d664dbbcf6f6c508b10eb4f/thumbnail/', '00:04:00', 3905, 'published', '2025-04-12 09:18:56', '2025-04-12 09:22:10'),
(4, 2, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', NULL, NULL, 'https://rutube.ru/api/video/ac187cc09d664dbbcf6f6c508b10eb4f/thumbnail/', '00:04:00', 3900, 'published', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(5, 3, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', NULL, NULL, 'https://rutube.ru/api/video/ac187cc09d664dbbcf6f6c508b10eb4f/thumbnail/', '00:04:00', 3900, 'published', '2025-04-12 09:18:56', '2025-04-12 09:18:56'),
(6, 3, 'Как создать сайт на Python', 'Бесплатные уроки по созданию сайтов на Python для начинающих', 'https://rutube.ru/play/embed/ac187cc09d664dbbcf6f6c508b10eb4f/', NULL, NULL, 'https://rutube.ru/api/video/ac187cc09d664dbbcf6f6c508b10eb4f/thumbnail/', '00:04:00', 3900, 'published', '2025-04-12 09:18:56', '2025-04-12 09:18:56');

-- --------------------------------------------------------

--
-- Структура таблицы `video_tags`
--

CREATE TABLE `video_tags` (
  `id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `video_tags`
--

INSERT INTO `video_tags` (`id`, `video_id`, `name`, `created_at`) VALUES
(1, 1, 'программирование', '2025-04-12 09:18:56'),
(2, 1, 'python', '2025-04-12 09:18:56'),
(3, 1, 'веб-разработка', '2025-04-12 09:18:56'),
(4, 1, 'обучение', '2025-04-12 09:18:56'),
(5, 2, 'программирование', '2025-04-12 09:18:56'),
(6, 2, 'python', '2025-04-12 09:18:56'),
(7, 2, 'веб-разработка', '2025-04-12 09:18:56'),
(8, 2, 'обучение', '2025-04-12 09:18:56'),
(9, 3, 'программирование', '2025-04-12 09:18:56'),
(10, 3, 'python', '2025-04-12 09:18:56'),
(11, 3, 'веб-разработка', '2025-04-12 09:18:56'),
(12, 3, 'обучение', '2025-04-12 09:18:56'),
(13, 4, 'программирование', '2025-04-12 09:18:56'),
(14, 4, 'python', '2025-04-12 09:18:56'),
(15, 4, 'веб-разработка', '2025-04-12 09:18:56'),
(16, 4, 'обучение', '2025-04-12 09:18:56'),
(17, 5, 'программирование', '2025-04-12 09:18:56'),
(18, 5, 'python', '2025-04-12 09:18:56'),
(19, 5, 'веб-разработка', '2025-04-12 09:18:56'),
(20, 5, 'обучение', '2025-04-12 09:18:56'),
(21, 6, 'программирование', '2025-04-12 09:18:56'),
(22, 6, 'python', '2025-04-12 09:18:56'),
(23, 6, 'веб-разработка', '2025-04-12 09:18:56'),
(24, 6, 'обучение', '2025-04-12 09:18:56');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_comments_video` (`video_id`);

--
-- Индексы таблицы `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `video_user` (`video_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `college_id` (`college_id`);

--
-- Индексы таблицы `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_videos_college` (`college_id`),
  ADD KEY `idx_videos_status` (`status`);

--
-- Индексы таблицы `video_tags`
--
ALTER TABLE `video_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_video_tags_video` (`video_id`),
  ADD KEY `idx_video_tags_name` (`name`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `colleges`
--
ALTER TABLE `colleges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `video_tags`
--
ALTER TABLE `video_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `video_tags`
--
ALTER TABLE `video_tags`
  ADD CONSTRAINT `video_tags_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
