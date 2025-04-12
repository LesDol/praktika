<?php
// Handle user management
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'delete_user':
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_POST['user_id']]);
            break;
            
        case 'update_role':
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$_POST['role'], $_POST['user_id']]);
            break;
            
        case 'delete_video':
            $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
            $stmt->execute([$_POST['video_id']]);
            break;
            
        case 'update_video_status':
            $stmt = $pdo->prepare("UPDATE videos SET status = ? WHERE id = ?");
            $stmt->execute([$_POST['status'], $_POST['video_id']]);
            break;
    }
}

// Get all users
$stmt = $pdo->query("SELECT u.*, c.name as college_name 
                     FROM users u 
                     LEFT JOIN colleges c ON u.college_id = c.id 
                     ORDER BY u.created_at DESC");
$users = $stmt->fetchAll();

// Get all videos
$stmt = $pdo->query("SELECT v.*, c.name as college_name 
                     FROM videos v 
                     JOIN colleges c ON v.college_id = c.id 
                     ORDER BY v.created_at DESC");
$videos = $stmt->fetchAll();

// Get all colleges
$stmt = $pdo->query("SELECT * FROM colleges");
$colleges = $stmt->fetchAll();

// Handle video upload
if (isset($_POST['action']) && $_POST['action'] === 'add_video') {
    $college_id = $_POST['college_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $url = $_POST['url'];
    
    // Проверяем URL Rutube
    if (!preg_match('/^https:\/\/rutube\.ru\/video\/[a-zA-Z0-9]+\/$/', $url)) {
        die('Неверный формат URL Rutube');
    }
    
    // Извлекаем ID видео из URL
    preg_match('/\/video\/([a-zA-Z0-9]+)\/$/', $url, $matches);
    $video_id = $matches[1];
    
    // Формируем URL для встраивания и превьюшки
    $embed_url = 'https://rutube.ru/play/embed/' . $video_id;
    $thumbnail_url = 'https://rutube.ru/api/video/' . $video_id . '/thumbnail/';
    
    // Добавляем запись в базу данных
    try {
        $stmt = $pdo->prepare("INSERT INTO videos (college_id, title, description, url, thumbnail_url, duration, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $college_id, 
            $title, 
            $description, 
            $embed_url,
            $thumbnail_url,
            $duration
        ]);
        
        echo '<div class="alert alert-success">Видео успешно добавлено!</div>';
    } catch (PDOException $e) {
        die('Ошибка при сохранении в базу данных: ' . $e->getMessage());
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <h1>Панель администратора</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Пользователи</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Колледж</th>
                                <th>Роль</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['college_name'] ?? 'Нет'); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update_role">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Админ</option>
                                                <option value="college" <?php echo $user['role'] === 'college' ? 'selected' : ''; ?>>Колледж</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Удалить пользователя?')">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Видео</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Колледж</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($videos as $video): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($video['title']); ?></td>
                                    <td><?php echo htmlspecialchars($video['college_name']); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update_video_status">
                                            <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $video['status'] === 'pending' ? 'selected' : ''; ?>>На модерации</option>
                                                <option value="published" <?php echo $video['status'] === 'published' ? 'selected' : ''; ?>>Опубликовано</option>
                                                <option value="rejected" <?php echo $video['status'] === 'rejected' ? 'selected' : ''; ?>>Отклонено</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Удалить видео?')">
                                            <input type="hidden" name="action" value="delete_video">
                                            <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_POST['action']) && $_POST['action'] === 'add_video'): ?>
    <div class="card">
        <div class="card-header">
            <h3>Добавить новое видео</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_video">
                <div class="mb-3">
                    <label for="college_id" class="form-label">Колледж</label>
                    <select class="form-select" id="college_id" name="college_id" required>
                        <?php foreach ($colleges as $college): ?>
                            <option value="<?php echo $college['id']; ?>"><?php echo htmlspecialchars($college['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="title" class="form-label">Название</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="url" class="form-label">URL видео с Rutube</label>
                    <input type="text" class="form-control" id="url" name="url" placeholder="https://rutube.ru/video/..." required>
                    <small class="text-muted">Пример: https://rutube.ru/video/1234567890/</small>
                </div>
                <div class="mb-3">
                    <label for="duration" class="form-label">Длительность (чч:мм:сс)</label>
                    <input type="text" class="form-control" id="duration" name="duration" pattern="\d{2}:\d{2}:\d{2}" placeholder="00:00:00" required>
                </div>
                <button type="submit" class="btn btn-primary">Добавить видео</button>
            </form>
        </div>
    </div>
<?php endif; ?> 