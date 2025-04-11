<?php
/**
 * Helper functions for the College Videos platform
 */

/**
 * Generate a clean URL-friendly slug from a string
 * 
 * @param string $string The string to convert to a slug
 * @return string The slug
 */
function generateSlug($string) {
    $string = mb_strtolower($string, 'UTF-8');
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', ' ', $string);
    $string = preg_replace('/\s/', '-', $string);
    return $string;
}

/**
 * Format a date in a readable format
 * 
 * @param string $date The date to format
 * @param string $format The format to use
 * @return string The formatted date
 */
function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

/**
 * Format views count (e.g. 1000 -> 1K)
 * 
 * @param int $views The number of views
 * @return string Formatted views
 */
function formatViews($views) {
    if ($views >= 1000000) {
        return round($views / 1000000, 1) . 'M';
    } else if ($views >= 1000) {
        return round($views / 1000, 1) . 'K';
    }
    return $views;
}

/**
 * Check if user is logged in
 * 
 * @return bool Whether user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is an admin
 * 
 * @return bool Whether user is an admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if user is a college
 * 
 * @return bool Whether user is a college
 */
function isCollege() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'college';
}

/**
 * Get user ID from session
 * 
 * @return int|null User ID or null if not logged in
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get college ID from session
 * 
 * @return int|null College ID or null if not a college
 */
function getCollegeId() {
    return $_SESSION['college_id'] ?? null;
}

/**
 * Convert YouTube URL to embedded format
 * 
 * @param string $url YouTube URL
 * @return string YouTube embed URL or original URL if not YouTube
 */
function getYoutubeEmbedUrl($url) {
    $videoId = '';
    
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $videoId = $matches[1];
    } else if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $videoId = $matches[1];
    }
    
    if ($videoId) {
        return "https://www.youtube.com/embed/$videoId";
    }
    
    return $url;
}

/**
 * Get YouTube video thumbnail
 * 
 * @param string $url YouTube URL
 * @return string|null Thumbnail URL or null
 */
function getYoutubeThumbnail($url) {
    $videoId = '';
    
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $videoId = $matches[1];
    } else if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        $videoId = $matches[1];
    }
    
    if ($videoId) {
        return "https://img.youtube.com/vi/$videoId/mqdefault.jpg";
    }
    
    return null;
}

/**
 * Sanitize input data
 * 
 * @param string $data Input to sanitize
 * @return string Sanitized input
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Log user activity
 * 
 * @param PDO $pdo Database connection
 * @param string $action Action performed
 * @param string $entityType Type of entity affected
 * @param int $entityId ID of entity affected
 * @param string $details Additional details
 * @return bool Success
 */
function logActivity($pdo, $action, $entityType = null, $entityId = null, $details = null) {
    try {
        $userId = getUserId();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, entity_type, entity_id, details, ip_address, user_agent) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $action, $entityType, $entityId, $details, $ipAddress, $userAgent]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get user display name
 * 
 * @param PDO $pdo Database connection
 * @param int $userId User ID
 * @return string User email or college name
 */
function getUserDisplayName($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT u.email, c.name as college_name 
                          FROM users u 
                          LEFT JOIN colleges c ON u.college_id = c.id 
                          WHERE u.id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        return $user['college_name'] ?? $user['email'];
    }
    
    return 'Unknown user';
}

/**
 * Check if a video belongs to a college
 * 
 * @param PDO $pdo Database connection
 * @param int $videoId Video ID
 * @param int $collegeId College ID
 * @return bool Whether video belongs to college
 */
function isVideoOwnedByCollege($pdo, $videoId, $collegeId) {
    $stmt = $pdo->prepare("SELECT id FROM videos WHERE id = ? AND college_id = ?");
    $stmt->execute([$videoId, $collegeId]);
    return (bool)$stmt->fetch();
}

/**
 * Get video status label
 * 
 * @param string $status Status value
 * @return string HTML for status badge
 */
function getVideoStatusBadge($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge bg-warning text-dark">На модерации</span>';
        case 'published':
            return '<span class="badge bg-success">Опубликовано</span>';
        case 'rejected':
            return '<span class="badge bg-danger">Отклонено</span>';
        default:
            return '<span class="badge bg-secondary">Неизвестно</span>';
    }
}

/**
 * Truncate text to a certain length
 * 
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @return string Truncated text
 */
function truncateText($text, $length = 100) {
    if (mb_strlen($text, 'UTF-8') > $length) {
        return mb_substr($text, 0, $length, 'UTF-8') . '...';
    }
    return $text;
}

/**
 * Get list of most popular tags
 * 
 * @param PDO $pdo Database connection
 * @param int $limit Maximum number of tags to return
 * @return array Tags with counts
 */
function getPopularTags($pdo, $limit = 10) {
    $stmt = $pdo->query("SELECT name, COUNT(*) as count 
                         FROM video_tags 
                         GROUP BY name 
                         ORDER BY count DESC 
                         LIMIT $limit");
    return $stmt->fetchAll();
}
?> 