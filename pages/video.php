<?php
$video_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get video info
$stmt = $pdo->prepare("SELECT v.*, c.name as college_name, GROUP_CONCAT(DISTINCT vt.name) as tags 
                       FROM videos v 
                       JOIN colleges c ON v.college_id = c.id 
                       LEFT JOIN video_tags vt ON v.id = vt.video_id 
                       WHERE v.id = ? 
                       GROUP BY v.id");
$stmt->execute([$video_id]);
$video = $stmt->fetch();

if (!$video) {
    header('Location: index.php?page=colleges');
    exit;
}

// Increment views
$stmt = $pdo->prepare("UPDATE videos SET views = views + 1 WHERE id = ?");
$stmt->execute([$video_id]);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && $isLoggedIn && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        // Проверяем существование пользователя
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND status = 'active'");
        $stmt->execute([$_SESSION['user_id']]);
        if ($stmt->fetch()) {
            // Добавляем комментарий
            $stmt = $pdo->prepare("INSERT INTO comments (video_id, user_id, text) VALUES (?, ?, ?)");
            $stmt->execute([$video_id, $_SESSION['user_id'], $comment]);
            
            // Перенаправляем, чтобы избежать повторной отправки формы
            header("Location: index.php?page=video&id=" . $video_id);
            exit;
        }
    }
}

// Get comments
$stmt = $pdo->prepare("SELECT c.*, u.email as user_email 
                       FROM comments c 
                       JOIN users u ON c.user_id = u.id 
                       WHERE c.video_id = ? 
                       ORDER BY c.created_at DESC");
$stmt->execute([$video_id]);
$comments = $stmt->fetchAll();
?>

<style>
.video-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    height: 0;
    overflow: hidden;
    max-width: 100%;
    background: #000;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.video-container iframe,
.video-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

.video-info {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
}

.comments-section {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

@media (min-width: 992px) {
    .video-container {
        padding-bottom: 42.85%; /* Увеличенная высота для больших экранов */
    }
}

/* Стили для видео плеера */
video {
    width: 100%;
    height: 100%;
    background: #000;
}

/* Кастомные элементы управления */
.video-controls {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.7);
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.video-controls button {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 5px 10px;
}

.video-controls button:hover {
    background: rgba(255, 255, 255, 0.1);
}

.progress-bar {
    flex-grow: 1;
    height: 5px;
    background: rgba(255, 255, 255, 0.3);
    cursor: pointer;
    position: relative;
}

.progress {
    height: 100%;
    background: #fff;
    width: 0;
}
</style>

<div class="row">
    <div class="col-lg-9">
        <div class="video-info">
            <h2><?php echo htmlspecialchars($video['title']); ?></h2>
            <p class="text-muted">
                Колледж: <?php echo htmlspecialchars($video['college_name']); ?><br>
                Дата: <?php echo date('d.m.Y', strtotime($video['created_at'])); ?><br>
                Просмотры: <?php echo $video['views']; ?><br>
                Тэги: <?php echo $video['tags'] ? htmlspecialchars($video['tags']) : 'Нет тэгов'; ?>
            </p>
            
            <div class="video-container">
                <?php if ($video['file_path']): ?>
                    <video id="videoPlayer" controls>
                        <source src="<?php echo htmlspecialchars($video['file_path']); ?>" type="<?php echo htmlspecialchars($video['file_type']); ?>">
                        Ваш браузер не поддерживает видео.
                    </video>
                <?php else: ?>
                    <iframe 
                        src="<?php echo htmlspecialchars($video['url']); ?>" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                <?php endif; ?>
            </div>
            
            <p class="mt-4"><?php echo nl2br(htmlspecialchars($video['description'] ?? '')); ?></p>
        </div>
    </div>
    
    <div class="col-lg-3">
        <div class="comments-section">
            <h3>Комментарии</h3>
            
            <?php if ($isLoggedIn): ?>
                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <textarea class="form-control" name="comment" rows="3" placeholder="Ваш комментарий" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    <a href="index.php?page=login">Войдите</a>, чтобы оставить комментарий.
                </div>
            <?php endif; ?>
            
            <div class="comments-list" style="max-height: 600px; overflow-y: auto;">
                <?php foreach ($comments as $comment): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">
                                <?php echo htmlspecialchars($comment['user_email']); ?>
                                <small class="text-muted"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></small>
                            </h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['text'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($comments)): ?>
                    <div class="alert alert-info">
                        Комментариев пока нет.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Инициализация видеоплеера
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('videoPlayer');
    if (video) {
        // Добавляем обработчики событий для видео
        video.addEventListener('play', function() {
            console.log('Видео запущено');
        });
        
        video.addEventListener('pause', function() {
            console.log('Видео приостановлено');
        });
        
        video.addEventListener('ended', function() {
            console.log('Видео завершено');
        });
        
        // Обработка ошибок
        video.addEventListener('error', function() {
            console.error('Ошибка воспроизведения видео');
        });
    }
});
</script> 