<?php
// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$tag = isset($_GET['tag']) ? $_GET['tag'] : '';

// Build query
$query = "SELECT c.*, COUNT(v.id) as video_count, GROUP_CONCAT(DISTINCT vt.name) as tags 
          FROM colleges c 
          LEFT JOIN videos v ON c.id = v.college_id AND v.status = 'published'
          LEFT JOIN video_tags vt ON v.id = vt.video_id 
          WHERE 1=1";

$params = [];

if ($search) {
    $query .= " AND c.name LIKE ?";
    $params[] = "%$search%";
}

if ($tag) {
    $query .= " AND vt.name = ?";
    $params[] = $tag;
}

$query .= " GROUP BY c.id ORDER BY video_count DESC, c.name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$colleges = $stmt->fetchAll();

// Get all unique tags
$stmt = $pdo->query("SELECT DISTINCT name FROM video_tags ORDER BY name");
$allTags = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get popular tags with counts
$popularTags = getPopularTags($pdo, 5);
?>

<div class="colleges-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1>Список колледжей</h1>
            <p class="lead">Найдите учебное заведение и просмотрите его образовательные видеоматериалы</p>
        </div>
        <div class="col-md-6">
            <form method="GET" class="search-form">
                <input type="hidden" name="page" value="colleges">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Поиск по названию" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Поиск
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="filter-sidebar">
            <div class="filter-header">
                <h5><i class="bi bi-funnel me-2"></i>Фильтры</h5>
            </div>
            
            <div class="filter-section">
                <h6>Тэги</h6>
                <div class="tag-cloud mb-3">
                    <a href="?page=colleges<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="tag-item <?php echo empty($tag) ? 'active' : ''; ?>">
                        Все
                    </a>
                    <?php foreach ($popularTags as $t): ?>
                        <a href="?page=colleges&tag=<?php echo urlencode($t['name']); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="tag-item <?php echo $tag === $t['name'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($t['name']); ?> 
                            <span class="tag-count"><?php echo $t['count']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($allTags) > count($popularTags)): ?>
                    <div class="dropdown mb-3">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            Все тэги
                        </button>
                        <ul class="dropdown-menu tag-dropdown">
                            <?php foreach ($allTags as $t): ?>
                                <li>
                                    <a class="dropdown-item <?php echo $tag === $t ? 'active' : ''; ?>" 
                                       href="?page=colleges&tag=<?php echo urlencode($t); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                        <?php echo htmlspecialchars($t); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="filter-section">
                <h6>Поиск</h6>
                <form method="GET">
                    <input type="hidden" name="page" value="colleges">
                    <?php if ($tag): ?>
                        <input type="hidden" name="tag" value="<?php echo htmlspecialchars($tag); ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Название колледжа" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search me-1"></i> Найти
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if ($search || $tag): ?>
                <div class="filter-section">
                    <div class="d-grid">
                        <a href="?page=colleges" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Сбросить фильтры
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-9">
        <?php if ($search || $tag): ?>
            <div class="filter-summary mb-3">
                <span>Результаты поиска: </span>
                <?php if ($search): ?>
                    <span class="filter-badge">
                        Название: <?php echo htmlspecialchars($search); ?>
                        <a href="?page=colleges<?php echo $tag ? '&tag=' . urlencode($tag) : ''; ?>" class="badge-remove">×</a>
                    </span>
                <?php endif; ?>
                
                <?php if ($tag): ?>
                    <span class="filter-badge">
                        Тэг: <?php echo htmlspecialchars($tag); ?>
                        <a href="?page=colleges<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="badge-remove">×</a>
                    </span>
                <?php endif; ?>
                
                <span class="filter-count"><?php echo count($colleges); ?> колледж(ей)</span>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <?php if (empty($colleges)): ?>
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> По вашему запросу не найдено колледжей. Попробуйте изменить параметры поиска.
                    </div>
                </div>
            <?php endif; ?>
            
            <?php foreach ($colleges as $college): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 zoom-in">
                        <?php if (!empty($college['logo_url'])): ?>
                            <div class="card-img-container">
                                <img src="<?php echo htmlspecialchars($college['logo_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($college['name']); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title"><?php echo htmlspecialchars($college['name']); ?></h5>
                                <span class="badge bg-primary rounded-pill">
                                    <i class="bi bi-camera-video"></i> <?php echo $college['video_count']; ?>
                                </span>
                            </div>
                            
                            <p class="card-text">
                                <?php echo truncateText(htmlspecialchars($college['description'] ?? 'Нет описания'), 120); ?>
                            </p>
                            
                            <?php if ($college['tags']): ?>
                                <div class="college-tags">
                                    <?php 
                                    $tags = explode(',', $college['tags']);
                                    $tags = array_unique($tags);
                                    $tags = array_slice($tags, 0, 5); // Show only first 5 tags
                                    ?>
                                    <?php foreach ($tags as $t): ?>
                                        <a href="?page=colleges&tag=<?php echo urlencode($t); ?>" class="college-tag">
                                            <?php echo htmlspecialchars($t); ?>
                                        </a>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count(explode(',', $college['tags'])) > 5): ?>
                                        <span class="more-tags">+<?php echo count(explode(',', $college['tags'])) - 5; ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="index.php?page=college&id=<?php echo $college['id']; ?>" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Подробнее
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.colleges-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    padding: 2rem;
    border-radius: var(--border-radius);
    color: white;
    margin-bottom: 2rem;
    box-shadow: var(--box-shadow);
}

.colleges-header h1 {
    margin-bottom: 0.5rem;
    color: white;
}

.search-form .form-control {
    height: 50px;
    border-radius: 25px 0 0 25px;
    border: none;
    padding-left: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.search-form .btn {
    border-radius: 0 25px 25px 0;
    padding-left: 20px;
    padding-right: 20px;
}

.filter-sidebar {
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 1.5rem;
    position: sticky;
    top: 20px;
}

.filter-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.filter-section {
    margin-bottom: 1.5rem;
}

.filter-section h6 {
    margin-bottom: 1rem;
    font-weight: 600;
}

.tag-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag-item {
    display: inline-block;
    padding: 0.3rem 0.75rem;
    background-color: #f8f9fa;
    color: #495057;
    border-radius: 30px;
    font-size: 0.85rem;
    text-decoration: none;
    transition: all 0.2s ease;
}

.tag-item:hover {
    background-color: #e9ecef;
    color: var(--primary-color);
}

.tag-item.active {
    background-color: var(--primary-color);
    color: white;
}

.tag-count {
    display: inline-block;
    margin-left: 3px;
    font-size: 0.75rem;
    background-color: rgba(255,255,255,0.3);
    border-radius: 10px;
    padding: 0.1rem 0.4rem;
}

.tag-dropdown {
    max-height: 250px;
    overflow-y: auto;
    width: 100%;
}

.filter-summary {
    background-color: #f8f9fa;
    padding: 0.75rem 1rem;
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
}

.filter-badge {
    background-color: #e9ecef;
    color: #495057;
    border-radius: 20px;
    padding: 0.3rem 0.8rem;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    margin-right: 0.5rem;
}

.badge-remove {
    margin-left: 0.5rem;
    color: #9aa0a6;
    text-decoration: none;
    font-weight: bold;
}

.badge-remove:hover {
    color: #dc3545;
}

.filter-count {
    margin-left: auto;
    font-weight: 500;
    color: var(--primary-color);
}

.college-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
    margin-top: 1rem;
}

.college-tag {
    display: inline-block;
    padding: 0.2rem 0.5rem;
    background-color: #f0f4ff;
    color: var(--primary-color);
    border-radius: 4px;
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.2s ease;
}

.college-tag:hover {
    background-color: #e1e7ff;
}

.more-tags {
    display: inline-block;
    padding: 0.2rem 0.5rem;
    background-color: #f8f9fa;
    color: #6c757d;
    border-radius: 4px;
    font-size: 0.75rem;
}
</style> 