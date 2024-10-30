<?php
// route.php - A simple router for your PHP application

$request = $_SERVER['REQUEST_URI'];

// Clean up request URI
$request = strtok($request, '?'); // Remove query parameters

switch ($request) {
    case '/':
        require __DIR__ . '/index.php';
        break;

    // Login routes
    case '/login':
        require __DIR__ . '/src/php/login/login.php';
        break;

    case '/logout':
        require __DIR__ . '/src/php/login/logout.php';
        break;

    case '/callback':
        require __DIR__ . '/src/php/login/callback.php';
        break;

    // Profil routes
    case '/profil':
        require __DIR__ . '/src/php/profil/profil.php';
        break;

    case '/profile_detail':
        require __DIR__ . '/src/php/profil/profile_detail.php';
        break;

    case '/public_profiles':
        require __DIR__ . '/src/php/profil/public_profiles.php';
        break;

    // PDF routes
    case '/download_cv':
        require __DIR__ . '/src/php/pdf/download_cv.php';
        break;

    case '/view_cv':
        require __DIR__ . '/src/php/pdf/view_cv.php';
        break;

    // Projects route
    case '/projects':
        require __DIR__ . '/src/php/projects.php';
        break;

    default:
        http_response_code(404);
        echo "Page not found";
        break;
}
