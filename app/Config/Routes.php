<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/*
 * --------------------------------------------------------------------
 * Authentication Routes
 * --------------------------------------------------------------------
 */
// User-facing login and registration
$routes->get('login', 'Auth\AuthController::showUserLogin');
$routes->post('login', 'Auth\AuthController::login', ['filter' => 'ratelimit']);
$routes->get('register', 'Auth\AuthController::showUserRegister');
$routes->post('register', 'Auth\AuthController::register', ['filter' => 'ratelimit']);
$routes->get('logout', 'Auth\AuthController::logout', ['filter' => 'auth']);
$routes->get('profile', 'ProfileController::index', ['filter' => 'auth']);
$routes->get('forgot-password', 'Auth\AuthController::showUserForgotPassword');
$routes->post('forgot-password', 'Auth\AuthController::userForgotPassword', ['filter' => 'ratelimit']);
$routes->get('reset-password', 'Auth\AuthController::showUserResetPassword');
$routes->post('reset-password', 'Auth\AuthController::resetPassword', ['filter' => 'ratelimit']);

$routes->group('auth', ['namespace' => 'App\Controllers\Auth'], function($routes) {
    // Registration (with rate limiting)
    $routes->get('register', 'AuthController::showRegister');
    $routes->post('register', 'AuthController::register', ['filter' => 'ratelimit']);
    
    // Login (with rate limiting)
    $routes->get('login', 'AuthController::showLogin');
    $routes->post('login', 'AuthController::login', ['filter' => 'ratelimit']);
    
    // Logout (requires authentication)
    $routes->get('logout', 'AuthController::logout', ['filter' => 'auth']);
    
    // Password Reset (with rate limiting)
    $routes->get('forgot-password', 'AuthController::showForgotPassword');
    $routes->post('forgot-password', 'AuthController::forgotPassword', ['filter' => 'ratelimit']);
    $routes->get('reset-password', 'AuthController::showResetPassword');
    $routes->post('reset-password', 'AuthController::resetPassword', ['filter' => 'ratelimit']);
    
    // Email verification
    $routes->get('verify-email/(:segment)', 'AuthController::verifyEmail/$1');
    $routes->get('resend-verification/(:num)', 'AuthController::resendVerification/$1');
});

/*
 * --------------------------------------------------------------------
 * Admin Routes (requires authentication and admin role)
 * --------------------------------------------------------------------
 */
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');
    
    // App Management
    $routes->get('apps', 'AppManagementController::index');
    $routes->get('apps/create', 'AppManagementController::create');
    $routes->post('apps/store', 'AppManagementController::store');
    $routes->get('apps/edit/(:num)', 'AppManagementController::edit/$1');
    $routes->post('apps/update/(:num)', 'AppManagementController::update/$1');
    $routes->post('apps/delete/(:num)', 'AppManagementController::delete/$1');
    $routes->post('apps/approve/(:num)', 'AppManagementController::approve/$1');
    $routes->post('apps/reject/(:num)', 'AppManagementController::reject/$1');
    
    // Review Moderation
    $routes->get('reviews', 'ReviewModerationController::index');
    $routes->post('reviews/approve/(:num)', 'ReviewModerationController::approve/$1');
    $routes->post('reviews/reject/(:num)', 'ReviewModerationController::reject/$1');
    $routes->post('reviews/delete/(:num)', 'ReviewModerationController::delete/$1');
    
    // Scam Report Verification
    $routes->get('scam-reports', 'ScamReportModerationController::index');
    $routes->post('scam-reports/verify/(:num)', 'ScamReportModerationController::verify/$1');
    $routes->post('scam-reports/reject/(:num)', 'ScamReportModerationController::reject/$1');
    $routes->post('scam-reports/update-risk/(:num)', 'ScamReportModerationController::updateRisk/$1');
    
    // User Management
    $routes->get('users', 'UserManagementController::index');
    $routes->get('users/view/(:num)', 'UserManagementController::view/$1');
    $routes->post('users/suspend/(:num)', 'UserManagementController::suspend/$1');
    $routes->post('users/reactivate/(:num)', 'UserManagementController::reactivate/$1');
    $routes->post('users/delete/(:num)', 'UserManagementController::delete/$1');
    $routes->post('users/verify/(:num)', 'UserManagementController::verify/$1');
    
    // Blog Management
    $routes->get('blog', 'BlogManagementController::index');
    $routes->get('blog/create', 'BlogManagementController::create');
    $routes->post('blog/store', 'BlogManagementController::store');
    $routes->get('blog/edit/(:num)', 'BlogManagementController::edit/$1');
    $routes->post('blog/update/(:num)', 'BlogManagementController::update/$1');
    $routes->post('blog/delete/(:num)', 'BlogManagementController::delete/$1');
    $routes->get('blog/publish/(:num)', 'BlogManagementController::publish/$1');
    $routes->get('blog/unpublish/(:num)', 'BlogManagementController::unpublish/$1');
    
    // Category Management
    $routes->get('categories', 'CategoryManagementController::index');
    $routes->get('categories/create', 'CategoryManagementController::create');
    $routes->post('categories/store', 'CategoryManagementController::store');
    $routes->get('categories/edit/(:num)', 'CategoryManagementController::edit/$1');
    $routes->post('categories/update/(:num)', 'CategoryManagementController::update/$1');
    $routes->post('categories/delete/(:num)', 'CategoryManagementController::delete/$1');
    
    // Settings
    $routes->get('settings', 'SettingsController::index');
    $routes->post('settings/update', 'SettingsController::update');
});

/*
 * --------------------------------------------------------------------
 * Public Routes (user-facing)
 * --------------------------------------------------------------------
 */
// App routes
$routes->get('apps', 'Public\AppController::index');
$routes->get('apps/(:segment)', 'AppController::show/$1');
$routes->get('app/(:segment)', 'AppController::show/$1');  // singular alias
$routes->post('apps/submit-review/(:num)', 'AppController::submitReview/$1', ['filter' => ['auth', 'ratelimit']]);
$routes->post('apps/submit-scam-report/(:num)', 'AppController::submitScamReport/$1', ['filter' => ['auth', 'ratelimit']]);

// Search
$routes->get('search', 'SearchController::index');

// Categories
$routes->get('categories', 'CategoryController::index');
$routes->get('categories/(:segment)', 'CategoryController::show/$1');

// Trending
$routes->get('trending', 'TrendingController::index');
$routes->get('trending/filter', 'TrendingController::filterByCategory');

// Scam Alerts
$routes->get('scam-alerts', 'ScamAlertController::index');
$routes->get('scam-alerts/report', 'ScamAlertController::reportForm');
$routes->post('scam-alerts/report', 'ScamAlertController::submitReport', ['filter' => ['auth', 'ratelimit']]);
$routes->get('scam-alerts/(:segment)', 'ScamAlertController::show/$1');

// Blog
$routes->get('blog', 'BlogController::index');
$routes->get('blog/(:segment)', 'BlogController::show/$1');

// Comparison Tool
$routes->get('comparison', 'Comparison::index');
$routes->post('comparison/add', 'Comparison::add');
$routes->get('comparison/remove/(:num)', 'Comparison::remove/$1');
$routes->get('comparison/clear', 'Comparison::clear');
$routes->get('comparison/search', 'Comparison::search');

// Review submission (requires authentication and rate limiting)
$routes->post('reviews/submit', 'Public\ReviewController::submit', ['filter' => ['auth', 'ratelimit']]);
$routes->post('reviews/helpful/(:num)', 'Public\ReviewController::markHelpful/$1', ['filter' => ['auth', 'ratelimit']]);

// Scam report submission (requires authentication and rate limiting)
$routes->post('scam-reports/submit', 'Public\ScamReportController::submit', ['filter' => ['auth', 'ratelimit']]);

// Newsletter subscription (with rate limiting)
$routes->post('newsletter/subscribe', 'NewsletterController::subscribe', ['filter' => 'ratelimit']);
$routes->get('newsletter/confirm/(:segment)', 'NewsletterController::confirm/$1');
$routes->get('newsletter/unsubscribe/(:segment)', 'NewsletterController::unsubscribePage/$1');
$routes->post('newsletter/unsubscribe/(:segment)', 'NewsletterController::unsubscribe/$1');

