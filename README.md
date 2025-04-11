# Система управления видео колледжей

Веб-платформа для управления и публикации видеоматериалов учебных заведений. Система позволяет колледжам регистрироваться, загружать видео, которые после модерации становятся доступны для просмотра всем пользователям.

## Функциональные возможности

- **Авторизация и регистрация**
  - Регистрация колледжей
  - Авторизация пользователей
  - Восстановление пароля через email

- **Управление контентом**
  - Загрузка видео колледжами
  - Модерация контента администраторами
  - Тегирование видео для удобного поиска

- **Просмотр контента**
  - Каталог колледжей с фильтрацией
  - Страницы колледжей с их видеоматериалами
  - Просмотр видео с возможностью комментирования

- **Администрирование**
  - Управление пользователями (колледжами)
  - Модерация видео и комментариев
  - Статистика просмотров

## Технологии

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5
- JavaScript

## Требования

- Веб-сервер (Apache/Nginx)
- PHP 7.4 или выше
- MySQL 5.7 или выше
- Поддержка PDO и MySQLi
- mod_rewrite для Apache (опционально)

## Установка

1. **Клонирование репозитория**

```bash
git clone https://github.com/yourusername/college-videos.git
cd college-videos
```

2. **Создание базы данных**

```bash
mysql -u root -p < database.sql
```

3. **Настройка конфигурации**

Отредактируйте файл `config/database.php`, указав данные для подключения к вашей базе данных:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'ваш_пользователь');
define('DB_PASS', 'ваш_пароль');
define('DB_NAME', 'college_videos');
```

4. **Настройка прав доступа**

```bash
chmod 755 -R assets/
mkdir uploads
chmod 777 -R uploads/
```

5. **Настройка веб-сервера**

**Apache**:
```apache
<VirtualHost *:80>
    ServerName videocolleges.local
    DocumentRoot /путь/к/проекту
    
    <Directory /путь/к/проекту>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx**:
```nginx
server {
    listen 80;
    server_name videocolleges.local;
    root /путь/к/проекту;
    
    location / {
        index index.php;
        try_files $uri $uri/ /index.php?$args;
    }
    
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## Использование

1. **Администратор**:
   - Email: admin@example.com
   - Пароль: password
   - Функции: управление пользователями, модерация контента

2. **Колледж**:
   - Регистрируется через форму регистрации
   - Функции: загрузка и управление своими видео

## Структура базы данных

- **colleges**: Информация о колледжах
- **users**: Пользователи системы
- **videos**: Видеоматериалы колледжей
- **video_tags**: Теги видео для поиска
- **comments**: Комментарии к видео
- **likes**: Лайки видео
- **audit_log**: Журнал действий в системе

## Лицензия

MIT

## Автор

Ваше имя