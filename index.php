<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Basic routing
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isCollege = isset($_SESSION['role']) && $_SESSION['role'] === 'college';

// Include header
include 'includes/header.php';

// Route handling
switch($page) {
    case 'login':
        include 'pages/login.php';
        break;
    case 'register':
        include 'pages/register.php';
        break;
    case 'registration-success':
        include 'pages/registration-success.php';
        break;
    case 'forgot-password':
        include 'pages/forgot-password.php';
        break;
    case 'reset-password':
        include 'pages/reset-password.php';
        break;
    case 'colleges':
        include 'pages/colleges.php';
        break;
    case 'college':
        include 'pages/college.php';
        break;
    case 'video':
        include 'pages/video.php';
        break;
    case 'college-videos':
        if ($isLoggedIn && $isCollege) {
            include 'pages/college-videos.php';
        } else {
            header('Location: index.php?page=login');
            exit;
        }
        break;
    case 'admin':
        if($isAdmin) {
            include 'pages/admin.php';
        } else {
            header('Location: index.php?page=login');
            exit;
        }
        break;
    default:
        include 'pages/home.php';
}

// Include footer
include 'includes/footer.php';
?> 