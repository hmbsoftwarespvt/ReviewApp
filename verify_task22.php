<?php

/**
 * Manual Verification Script for Task 22: App Detail Page
 * 
 * This script creates test data and verifies the app detail page functionality.
 * Run this script from the command line: php verify_task22.php
 */

// Load CodeIgniter
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

echo "=== Task 22 Verification Script ===\n\n";

// Step 1: Create test category
echo "1. Creating test category...\n";
$categoryModel = model('CategoryModel');
$categoryId = $categoryModel->insert([
    'name' => 'Test Category',
    'slug' => 'test-category-' . time(),
    'description' => 'Test category for verification',
    'display_order' => 1,
]);
echo "   ✓ Category created (ID: {$categoryId})\n\n";

// Step 2: Create test app
echo "2. Creating test app...\n";
$appModel = model('AppModel');
$slug = 'test-app-' . time();
$appId = $appModel->insert([
    'name' => 'Test App for Task 22',
    'slug' => $slug,
    'description' => 'This is a test app created to verify Task 22 implementation',
    'version' => '1.0.0',
    'size' => '25MB',
    'platform_type' => 'android',
    'price' => 0.00,
    'developer_name' => 'Test Developer',
    'release_date' => '2023-01-01',
    'download_url' => 'https://example.com/download',
    'trust_score' => 85.5,
    'security_score' => 20.0,
    'developer_reputation' => 15.0,
    'view_count' => 100,
    'approval_status' => 'approved',
]);
echo "   ✓ App created (ID: {$appId}, Slug: {$slug})\n\n";

// Step 3: Attach category to app
echo "3. Attaching category to app...\n";
$appModel->attachCategories($appId, [$categoryId]);
echo "   ✓ Category attached\n\n";

// Step 4: Create test user
echo "4. Creating test user...\n";
$userModel = model('UserModel');
$userId = $userModel->insert([
    'username' => 'testuser_' . time(),
    'email' => 'test_' . time() . '@example.com',
    'password_hash' => password_hash('password', PASSWORD_BCRYPT),
    'role' => 'user',
    'status' => 'active',
]);
echo "   ✓ User created (ID: {$userId})\n\n";

// Step 5: Create test reviews
echo "5. Creating test reviews...\n";
$reviewModel = model('ReviewModel');
for ($i = 1; $i <= 3; $i++) {
    $reviewId = $reviewModel->insert([
        'app_id' => $appId,
        'user_id' => $userId,
        'rating' => 4,
        'title' => "Test Review {$i}",
        'review_text' => str_repeat("This is test review {$i}. ", 10),
        'approval_status' => 'approved',
    ]);
    echo "   ✓ Review {$i} created (ID: {$reviewId})\n";
    
    // Create new user for next review (unique constraint)
    if ($i < 3) {
        $userId = $userModel->insert([
            'username' => 'testuser_' . time() . '_' . $i,
            'email' => 'test_' . time() . '_' . $i . '@example.com',
            'password_hash' => password_hash('password', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
    }
}
echo "\n";

// Step 6: Create test scam reports
echo "6. Creating test scam reports...\n";
$scamReportModel = model('ScamReportModel');
$scamReportId = $scamReportModel->insert([
    'app_id' => $appId,
    'user_id' => $userId,
    'title' => 'Test Scam Report',
    'description' => str_repeat('This is a test scam report. ', 20),
    'risk_level' => 'medium',
    'approval_status' => 'approved',
]);
echo "   ✓ Scam report created (ID: {$scamReportId})\n\n";

// Step 7: Create test screenshots
echo "7. Creating test screenshots...\n";
$screenshotModel = model('ScreenshotModel');
for ($i = 1; $i <= 3; $i++) {
    $screenshotId = $screenshotModel->insert([
        'app_id' => $appId,
        'filename' => "screenshot{$i}.jpg",
        'file_path' => "/uploads/screenshots/screenshot{$i}.jpg",
        'display_order' => $i,
    ]);
    echo "   ✓ Screenshot {$i} created (ID: {$screenshotId})\n";
}
echo "\n";

// Step 8: Create similar apps
echo "8. Creating similar apps...\n";
for ($i = 1; $i <= 7; $i++) {
    $similarAppId = $appModel->insert([
        'name' => "Similar App {$i}",
        'slug' => "similar-app-{$i}-" . time(),
        'description' => "Similar app {$i} for testing",
        'platform_type' => 'android',
        'developer_name' => 'Test Developer',
        'trust_score' => 70.0 + $i,
        'approval_status' => 'approved',
    ]);
    $appModel->attachCategories($similarAppId, [$categoryId]);
    echo "   ✓ Similar app {$i} created (ID: {$similarAppId})\n";
}
echo "\n";

// Step 9: Test RecommendationService
echo "9. Testing RecommendationService...\n";
$recommendationService = new \App\Services\RecommendationService();
$similarApps = $recommendationService->getSimilarApps($appId, 6);
echo "   ✓ Found " . count($similarApps) . " similar apps (expected: 6)\n";
if (count($similarApps) === 6) {
    echo "   ✓ Similar apps limit working correctly\n";
} else {
    echo "   ✗ Similar apps limit NOT working correctly\n";
}
echo "\n";

// Step 10: Test TrustScoreService
echo "10. Testing TrustScoreService...\n";
$trustScoreService = new \App\Services\TrustScoreService();
$breakdown = $trustScoreService->getTrustScoreBreakdown($appId);
echo "   ✓ Trust score breakdown retrieved\n";
echo "   - User Reviews: {$breakdown['review_rating']['score']} / {$breakdown['review_rating']['max_points']}\n";
echo "   - Security Analysis: {$breakdown['security_score']['score']} / {$breakdown['security_score']['max_points']}\n";
echo "   - Developer Reputation: {$breakdown['developer_reputation']['score']} / {$breakdown['developer_reputation']['max_points']}\n";
echo "   - Scam Reports: {$breakdown['scam_report_count']['score']} / {$breakdown['scam_report_count']['max_points']}\n";
echo "   - App Age: {$breakdown['app_age']['score']} / {$breakdown['app_age']['max_points']}\n";
echo "\n";

// Step 11: Test view count increment
echo "11. Testing view count increment...\n";
$app = $appModel->find($appId);
$initialViewCount = $app['view_count'];
echo "   Initial view count: {$initialViewCount}\n";

$appRepository = new \App\Repositories\AppRepository();
$appRepository->incrementViewCount($appId);

$app = $appModel->find($appId);
$newViewCount = $app['view_count'];
echo "   New view count: {$newViewCount}\n";

if ($newViewCount === $initialViewCount + 1) {
    echo "   ✓ View count increment working correctly\n";
} else {
    echo "   ✗ View count increment NOT working correctly\n";
}
echo "\n";

// Step 12: Display URL to test
echo "=== Verification Complete ===\n\n";
echo "Test app created successfully!\n";
echo "Visit the following URL to test the app detail page:\n";
echo base_url("apps/{$slug}") . "\n\n";

echo "Expected features:\n";
echo "✓ App information displayed (name, developer, version, etc.)\n";
echo "✓ Trust score badge with color (green for 85.5)\n";
echo "✓ Trust score breakdown with 5 components\n";
echo "✓ 3 screenshots in gallery\n";
echo "✓ 3 approved reviews\n";
echo "✓ 1 approved scam report\n";
echo "✓ 6 similar apps in sidebar\n";
echo "✓ View count increments on each visit\n\n";

echo "Cleanup: To remove test data, run the following SQL:\n";
echo "DELETE FROM apps WHERE id >= {$appId};\n";
echo "DELETE FROM categories WHERE id = {$categoryId};\n";
echo "DELETE FROM users WHERE id >= {$userId};\n";
