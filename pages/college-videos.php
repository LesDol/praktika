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
                <form method="POST" enctype="multipart/form-data">
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
                        <label class="form-label">Тип видео</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="type_url" value="url" checked>
                            <label class="form-check-label" for="type_url">
                                Ссылка на видео
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="type_file" value="file">
                            <label class="form-check-label" for="type_file">
                                Загрузить файл
                            </label>
                        </div>
                    </div>
                    
                    <div id="url_input" class="mb-3">
                        <label for="url" class="form-label">URL видео</label>
                        <input type="url" class="form-control" id="url" name="url">
                        <small class="text-muted">Укажите прямую ссылку на видео с Rutube</small>
                    </div>
                    
                    <div id="file_input" class="mb-3" style="display: none;">
                        <label for="video_file" class="form-label">Видео файл</label>
                        <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*">
                        <small class="text-muted">Поддерживаемые форматы: MP4, WebM, OGG (максимальный размер: 100MB)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Тэги</label>
                        <input type="text" class="form-control" id="tags" name="tags">
                        <small class="text-muted">Введите тэги через запятую</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Добавить видео</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php foreach ($videos as $video): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <?php if ($video['thumbnail_url']): ?>
                    <img src="<?php echo htmlspecialchars($video['thumbnail_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($video['title']); ?>">
                <?php else: ?>
                    <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-video fa-3x"></i>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($video['title']); ?></h5>
                    <p class="card-text">
                        <small class="text-muted">
                            Дата: <?php echo date('d.m.Y', strtotime($video['created_at'])); ?><br>
                            Просмотры: <?php echo $video['views']; ?><br>
                            Тэги: <?php echo $video['tags'] ? htmlspecialchars($video['tags']) : 'Нет тэгов'; ?>
                        </small>
                    </p>
                    <a href="index.php?page=video&id=<?php echo $video['id']; ?>" class="btn btn-primary">Смотреть</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
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
    const typeUrl = document.getElementById('type_url');
    const typeFile = document.getElementById('type_file');
    const urlInput = document.getElementById('url_input');
    const fileInput = document.getElementById('file_input');
    
    function toggleInputs() {
        if (typeUrl.checked) {
            urlInput.style.display = 'block';
            fileInput.style.display = 'none';
            document.getElementById('url').required = true;
            document.getElementById('video_file').required = false;
        } else {
            urlInput.style.display = 'none';
            fileInput.style.display = 'block';
            document.getElementById('url').required = false;
            document.getElementById('video_file').required = true;
        }
    }
    
    typeUrl.addEventListener('change', toggleInputs);
    typeFile.addEventListener('change', toggleInputs);
});
</script> 