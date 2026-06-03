<?php
session_start();

// Detect base path (e.g. /homestayv2/admin)
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$basePath = $basePath === '/' ? '' : $basePath;

// Remove leading base/admin from the requested path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$subPath = trim($path, '/');

$isAuthenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;

// Handle logout
if ($subPath === 'logout') {
    header('Location: logout.php');
    exit;
}

// Simple, hardcoded credentials (fallback if login.php is bypassed)
$validUser = 'admin';
$validPass = 'admin123';

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $subPath === '') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if ($user === $validUser && $pass === $validPass) {
        $_SESSION['admin_auth'] = true;
        header('Location: ' . ($basePath === '' ? '/admin/dashboard' : $basePath . '/dashboard'));
        exit;
    }
    $error = 'Invalid username or password.';
}

// Public routes inside /admin
$publicRoutes = ['', 'login'];

// Protect everything except the login page
if (!$isAuthenticated && !in_array($subPath, $publicRoutes, true)) {
    header('Location: ' . ($basePath === '' ? '/' : dirname($basePath)));
    exit;
}

// Route to views
switch ($subPath) {
    case '':
    case 'login':
        include __DIR__ . '/login.php';
        break;
    case 'dashboard':
        include __DIR__ . '/dashboard.php';
        break;
    default:
        http_response_code(404);
        include __DIR__ . '/not-found.php';
        break;
}
