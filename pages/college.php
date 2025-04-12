<?php
$college_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get college info
$stmt = $pdo->prepare("SELECT * FROM colleges WHERE id = ?");
$stmt->execute([$college_id]);
$college = $stmt->fetch();

if (!$college) {
    header('Location: index.php?page=colleges');
    exit;
}

// Get videos with sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';

$allowed_sorts = ['created_at', 'title', 'views'];
$sort = in_array($sort, $allowed_sorts) ? $sort : 'created_at';
$order = $order === 'asc' ? 'ASC' : 'DESC';

$query = "SELECT v.*, GROUP_CONCAT(DISTINCT vt.name) as tags 
          FROM videos v 
          LEFT JOIN video_tags vt ON v.id = vt.video_id 
          WHERE v.college_id = ? 
          GROUP BY v.id 
          ORDER BY v.$sort $order";

$stmt = $pdo->prepare($query);
$stmt->execute([$college_id]);
$videos = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-md-12">
        <h1><?php echo htmlspecialchars($college['name']); ?></h1>
        <p class="lead"><?php echo htmlspecialchars($college['description'] ?? ''); ?></p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Сортировка: <?php echo $sort === 'created_at' ? 'Дата' : ($sort === 'title' ? 'Название' : 'Просмотры'); ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?page=college&id=<?php echo $college_id; ?>&sort=created_at&order=<?php echo $order === 'ASC' ? 'desc' : 'asc'; ?>">Дата</a></li>
                <li><a class="dropdown-item" href="?page=college&id=<?php echo $college_id; ?>&sort=title&order=<?php echo $order === 'ASC' ? 'desc' : 'asc'; ?>">Название</a></li>
                <li><a class="dropdown-item" href="?page=college&id=<?php echo $college_id; ?>&sort=views&order=<?php echo $order === 'ASC' ? 'desc' : 'asc'; ?>">Просмотры</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <?php foreach ($videos as $video): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
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

<?php if (empty($videos)): ?>
    <div class="alert alert-info">
        У этого колледжа пока нет видео.
    </div>
<?php endif; ?> 