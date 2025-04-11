<?php
// Check if logged in as college
if (!$isLoggedIn || !isset($_SESSION['college_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$college_id = $_SESSION['college_id'];
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_video':
                $title = trim($_POST['title']);
                $description = trim($_POST['description']);
                $url = trim($_POST['url']);
                $tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
                
                // Validate input
                if (empty($title) || empty($url)) {
                    $error_message = "Название и URL видео обязательны";
                } else {
                    // Insert video
                    $stmt = $pdo->prepare("INSERT INTO videos (college_id, title, description, url) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$college_id, $title, $description, $url]);
                    $video_id = $pdo->lastInsertId();
                    
                    // Add tags
                    foreach ($tags as $tag) {
                        $tag = trim($tag);
                        if (!empty($tag)) {
                            $stmt = $pdo->prepare("INSERT INTO video_tags (video_id, name) VALUES (?, ?)");
                            $stmt->execute([$video_id, $tag]);
                        }
                    }
                    
                    $success_message = "Видео успешно добавлено и отправлено на модерацию";
                }
                break;
                
            case 'edit_video':
                $video_id = (int)$_POST['video_id'];
                $title = trim($_POST['title']);
                $description = trim($_POST['description']);
                $url = trim($_POST['url']);
                $tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
                
                // Validate input and ownership
                if (empty($title) || empty($url)) {
                    $error_message = "Название и URL видео обязательны";
                } else {
                    // Verify ownership
                    $stmt = $pdo->prepare("SELECT id FROM videos WHERE id = ? AND college_id = ?");
                    $stmt->execute([$video_id, $college_id]);
                    if ($stmt->fetch()) {
                        // Update video
                        $stmt = $pdo->prepare("UPDATE videos SET title = ?, description = ?, url = ?, status = 'pending' WHERE id = ?");
                        $stmt->execute([$title, $description, $url, $video_id]);
                        
                        // Delete existing tags
                        $stmt = $pdo->prepare("DELETE FROM video_tags WHERE video_id = ?");
                        $stmt->execute([$video_id]);
                        
                        // Add new tags
                        foreach ($tags as $tag) {
                            $tag = trim($tag);
                            if (!empty($tag)) {
                                $stmt = $pdo->prepare("INSERT INTO video_tags (video_id, name) VALUES (?, ?)");
                                $stmt->execute([$video_id, $tag]);
                            }
                        }
                        
                        $success_message = "Видео успешно обновлено и отправлено на повторную модерацию";
                    } else {
                        $error_message = "У вас нет прав на редактирование этого видео";
                    }
                }
                break;
                
            case 'delete_video':
                $video_id = (int)$_POST['video_id'];
                
                // Verify ownership
                $stmt = $pdo->prepare("SELECT id FROM videos WHERE id = ? AND college_id = ?");
                $stmt->execute([$video_id, $college_id]);
                if ($stmt->fetch()) {
                    // Delete video
                    $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
                    $stmt->execute([$video_id]);
                    
                    $success_message = "Видео успешно удалено";
                } else {
                    $error_message = "У вас нет прав на удаление этого видео";
                }
                break;
        }
    }
}

// Get college videos
$stmt = $pdo->prepare("SELECT v.*, GROUP_CONCAT(vt.name) as tags 
                      FROM videos v 
                      LEFT JOIN video_tags vt ON v.id = vt.video_id 
                      WHERE v.college_id = ? 
                      GROUP BY v.id 
                      ORDER BY v.created_at DESC");
$stmt->execute([$college_id]);
$videos = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1>Управление видео</h1>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Добавить новое видео</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_video">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Название</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Описание</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="url" class="form-label">URL видео</label>
                        <input type="url" class="form-control" id="url" name="url" required>
                        <small class="text-muted">Укажите прямую ссылку на видео файл или на YouTube</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Тэги</label>
                        <input type="text" class="form-control" id="tags" name="tags">
                        <small class="text-muted">Введите тэги через запятую (например: математика, физика, химия)</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Добавить видео</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Мои видео</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Дата</th>
                                <th>Статус</th>
                                <th>Просмотры</th>
                                <th>Тэги</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($videos as $video): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($video['title']); ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($video['created_at'])); ?></td>
                                    <td>
                                        <?php
                                        switch ($video['status']) {
                                            case 'pending':
                                                echo '<span class="badge bg-warning text-dark">На модерации</span>';
                                                break;
                                            case 'published':
                                                echo '<span class="badge bg-success">Опубликовано</span>';
                                                break;
                                            case 'rejected':
                                                echo '<span class="badge bg-danger">Отклонено</span>';
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $video['views']; ?></td>
                                    <td><?php echo $video['tags'] ? htmlspecialchars($video['tags']) : 'Нет тэгов'; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary edit-video-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editVideoModal" 
                                                    data-id="<?php echo $video['id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($video['title']); ?>"
                                                    data-description="<?php echo htmlspecialchars($video['description'] ?? ''); ?>"
                                                    data-url="<?php echo htmlspecialchars($video['url']); ?>"
                                                    data-tags="<?php echo htmlspecialchars($video['tags'] ?? ''); ?>">
                                                Редактировать
                                            </button>
                                            <a href="index.php?page=video&id=<?php echo $video['id']; ?>" class="btn btn-sm btn-info">Просмотр</a>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить это видео?')">
                                                <input type="hidden" name="action" value="delete_video">
                                                <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($videos)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">У вас пока нет видео</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Video Modal -->
<div class="modal fade" id="editVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать видео</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="edit_video">
                    <input type="hidden" name="video_id" id="edit_video_id">
                    
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Название</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Описание</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_url" class="form-label">URL видео</label>
                        <input type="url" class="form-control" id="edit_url" name="url" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_tags" class="form-label">Тэги</label>
                        <input type="text" class="form-control" id="edit_tags" name="tags">
                        <small class="text-muted">Введите тэги через запятую</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit video modal
    const editButtons = document.querySelectorAll('.edit-video-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_video_id').value = this.getAttribute('data-id');
            document.getElementById('edit_title').value = this.getAttribute('data-title');
            document.getElementById('edit_description').value = this.getAttribute('data-description');
            document.getElementById('edit_url').value = this.getAttribute('data-url');
            document.getElementById('edit_tags').value = this.getAttribute('data-tags');
        });
    });
});
</script> 