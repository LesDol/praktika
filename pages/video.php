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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && $isLoggedIn) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (video_id, user_id, text) VALUES (?, ?, ?)");
        $stmt->execute([$video_id, $_SESSION['user_id'], $comment]);
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

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h2><?php echo htmlspecialchars($video['title']); ?></h2>
                <p class="text-muted">
                    Колледж: <?php echo htmlspecialchars($video['college_name']); ?><br>
                    Дата: <?php echo date('d.m.Y', strtotime($video['created_at'])); ?><br>
                    Просмотры: <?php echo $video['views']; ?><br>
                    Тэги: <?php echo $video['tags'] ? htmlspecialchars($video['tags']) : 'Нет тэгов'; ?>
                </p>
                
                <div class="ratio ratio-16x9 mb-4">
                    <video controls>
                        <source src="<?php echo htmlspecialchars($video['url']); ?>" type="video/mp4">
                        Ваш браузер не поддерживает видео.
                    </video>
                </div>
                
                <p><?php echo nl2br(htmlspecialchars($video['description'] ?? '')); ?></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Комментарии</h3>
            </div>
            <div class="card-body">
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
                
                <?php foreach ($comments as $comment): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">
                                <?php echo htmlspecialchars($comment['user_email']); ?>
                                <small><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></small>
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