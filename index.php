<?php
session_start();

// Detect the app base path (e.g. /homestayv2 when not at web root)
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$basePath = $basePath === '/' ? '' : $basePath; // keep empty string for root

// Normalize requested path (strip query string and base path)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$slug = trim($path, '/');

// Default to home when visiting the app root
if ($slug === '') {
    $slug = 'home';
}

// Helper to build absolute links respecting the base path
$link = function (string $target = '') use ($basePath): string {
    $target = ltrim($target, '/');
    return ($basePath === '' ? '' : $basePath) . '/' . $target;
};

// List of public site pages mapped to their view files
$pages = [
    'home'    => __DIR__ . '/pages/home.php',
    'about'   => __DIR__ . '/pages/about.php',
    'rooms'   => __DIR__ . '/pages/rooms.php',
    'houserules' => __DIR__ . '/pages/houserules.php',
    'shop'    => __DIR__ . '/pages/shop.php',
    'contact' => __DIR__ . '/pages/contact.php',
    'carrent' => __DIR__ . '/pages/carrent.php',
    'cars'    => __DIR__ . '/pages/carrent.php',
    'carrent.php' => __DIR__ . '/pages/carrent.php',
    'activity' => __DIR__ . '/pages/activity.php',
    'activities' => __DIR__ . '/pages/activity.php',
    'community' => __DIR__ . '/pages/activity.php',
    'communty' => __DIR__ . '/pages/activity.php',
    'blog'    => __DIR__ . '/pages/blog.php',
    'blogs'   => __DIR__ . '/pages/blog.php',
    'blogdetails' => __DIR__ . '/pages/blogdetails.php',
    'activitydetails' => __DIR__ . '/pages/activitydetails.php',
    'eventdetails' => __DIR__ . '/pages/eventdetails.php',
    'privacy' => __DIR__ . '/pages/privacy.php',
    'terms' => __DIR__ . '/pages/privacy.php',
    'cookies' => __DIR__ . '/pages/privacy.php',
    'bookinginfo' => __DIR__ . '/pages/bookinginfo.php',
    'rules' => __DIR__ . '/pages/houserules.php',
    'impact' => __DIR__ . '/pages/impact.php',
];

// If the request is for /admin, let the admin front controller handle it
if (strpos($slug, 'admin') === 0 && is_dir(__DIR__ . '/admin')) {
    // Rely on admin/.htaccess to rewrite to admin/index.php.
    // This fallback helps when mod_rewrite is disabled.
    require __DIR__ . '/admin/index.php';
    exit;
}

// Serve matching page or a 404
if (isset($pages[$slug]) && file_exists($pages[$slug])) {
    $pageTitle = ucfirst($slug === 'home' ? 'Home' : $slug);
    // Make $link available to page templates
    $baseLink = $link;
    include $pages[$slug];
    exit;
}

// Handle 404
http_response_code(404);
$pageTitle = '404 - Page Not Found';
$baseLink = $link;
// If you have a custom 404 page, include it here.
// Otherwise, just show a simple message or redirect to home with 404 status
if (file_exists(__DIR__ . '/pages/404.php')) {
    include __DIR__ . '/pages/404.php';
} else {
    // Fallback: show home page but with 404 status to avoid "Soft 404"
    include __DIR__ . '/pages/home.php';
}
exit;
