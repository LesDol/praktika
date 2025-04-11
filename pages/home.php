<?php
// Get featured colleges (those with most videos)
$stmt = $pdo->query("SELECT c.*, COUNT(v.id) as video_count 
                     FROM colleges c 
                     LEFT JOIN videos v ON c.id = v.college_id 
                     WHERE v.status = 'published'
                     GROUP BY c.id 
                     ORDER BY video_count DESC 
                     LIMIT 3");
$featuredColleges = $stmt->fetchAll();

// Get latest videos
$stmt = $pdo->query("SELECT v.*, c.name as college_name 
                     FROM videos v 
                     JOIN colleges c ON v.college_id = c.id 
                     WHERE v.status = 'published'
                     ORDER BY v.created_at DESC 
                     LIMIT 6");
$latestVideos = $stmt->fetchAll();
?>

<div class="row mb-5">
    <div class="col-md-12">
        <div class="jumbotron">
            <h1>Платформа видео колледжей</h1>
            <p class="lead">Удобная платформа для публикации и просмотра образовательных видео от учебных заведений.</p>
            <a href="index.php?page=colleges" class="btn">Просмотреть все колледжи</a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="section-title">Популярные колледжи</h2>
        <div class="section-divider"></div>
    </div>
</div>

<div class="row mb-5">
    <?php foreach ($featuredColleges as $college): ?>
        <div class="col-md-4 mb-4">
            <div class="card zoom-in">
                <?php if (!empty($college['logo_url'])): ?>
                    <div class="card-img-container">
                        <img src="<?php echo htmlspecialchars($college['logo_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($college['name']); ?>">
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($college['name']); ?></h5>
                    <p class="card-text">
                        <?php echo truncateText(htmlspecialchars($college['description'] ?? 'Нет описания'), 100); ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary rounded-pill">
                            <i class="bi bi-camera-video"></i> <?php echo $college['video_count']; ?> видео
                        </span>
                        <?php if (!empty($college['location'])): ?>
                            <span class="text-muted small">
                                <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($college['location']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="index.php?page=college&id=<?php echo $college['id']; ?>" class="btn btn-primary w-100">Подробнее</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($featuredColleges)): ?>
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i> Пока нет колледжей с опубликованными видео.
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="section-title">Новые видео</h2>
        <div class="section-divider"></div>
    </div>
</div>

<div class="row">
    <?php foreach ($latestVideos as $video): ?>
        <div class="col-md-4 mb-4">
            <div class="card zoom-in">
                <div class="card-img-container">
                    <?php if (!empty($video['thumbnail_url'])): ?>
                        <img src="<?php echo htmlspecialchars($video['thumbnail_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($video['title']); ?>">
                    <?php else: ?>
                        <div class="placeholder-thumbnail d-flex justify-content-center align-items-center">
                            <i class="bi bi-play-circle fs-1"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($video['title']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">
                        <i class="bi bi-building"></i> <?php echo htmlspecialchars($video['college_name']); ?>
                    </h6>
                    <div class="d-flex justify-content-between mt-3">
                        <small class="text-muted">
                            <i class="bi bi-calendar3"></i> <?php echo date('d.m.Y', strtotime($video['created_at'])); ?>
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-eye"></i> <?php echo formatViews($video['views']); ?> просмотров
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="index.php?page=video&id=<?php echo $video['id']; ?>" class="btn btn-primary w-100">Смотреть</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($latestVideos)): ?>
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i> Пока нет опубликованных видео.
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($latestVideos)): ?>
        <div class="col-md-12 text-center mt-4">
            <a href="index.php?page=colleges" class="btn btn-outline-primary">Посмотреть все видео <i class="bi bi-arrow-right"></i></a>
        </div>
    <?php endif; ?>
</div>

<style>
.section-title {
    font-weight: 700;
    padding-left: 15px;
    border-left: 5px solid var(--primary-color);
}

.section-divider {
    height: 2px;
    background: linear-gradient(90deg, var(--primary-color), transparent);
    margin-bottom: 25px;
}

.card-img-container {
    height: 180px;
    overflow: hidden;
    position: relative;
}

.card-img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}

.placeholder-thumbnail {
    background: linear-gradient(135deg, #f5f7ff 0%, #eef1f9 100%);
    height: 100%;
    color: var(--primary-color);
}

.badge {
    font-weight: 500;
}
</style> 