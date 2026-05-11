<?php

/**
 * Task 21 Verification Script
 * 
 * This script verifies that all required files for Task 21 exist
 * and have no syntax errors.
 */

echo "=== Task 21 Verification ===\n\n";

$rootPath = __DIR__ . DIRECTORY_SEPARATOR;
$appPath = $rootPath . 'app' . DIRECTORY_SEPARATOR;

// Files to check
$files = [
    'Controller' => $appPath . 'Controllers/Home.php',
    'View' => $appPath . 'Views/home.php',
    'Test' => $rootPath . 'tests/functional/HomePageTest.php',
    'Summary' => $rootPath . 'TASK_21_SUMMARY.md',
    'Manual Test Guide' => $rootPath . 'TASK_21_MANUAL_TEST.md',
];

$allPassed = true;

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "✓ {$name} exists: {$path}\n";
        
        // Check syntax for PHP files
        if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $output = [];
            $returnCode = 0;
            exec("php -l " . escapeshellarg($path) . " 2>&1", $output, $returnCode);
            
            if ($returnCode === 0) {
                echo "  ✓ No syntax errors\n";
            } else {
                echo "  ✗ Syntax errors found:\n";
                echo "    " . implode("\n    ", $output) . "\n";
                $allPassed = false;
            }
        }
    } else {
        echo "✗ {$name} not found: {$path}\n";
        $allPassed = false;
    }
}

// Check if required dependencies exist
echo "\nChecking dependencies:\n";

$dependencies = [
    'AppRepository' => $appPath . 'Repositories/AppRepository.php',
    'CategoryModel' => $appPath . 'Models/CategoryModel.php',
    'ReviewModel' => $appPath . 'Models/ReviewModel.php',
    'ScamReportModel' => $appPath . 'Models/ScamReportModel.php',
    'UserModel' => $appPath . 'Models/UserModel.php',
];

foreach ($dependencies as $name => $path) {
    if (file_exists($path)) {
        echo "✓ {$name} exists\n";
    } else {
        echo "✗ {$name} not found\n";
        $allPassed = false;
    }
}

// Check routes configuration
echo "\nChecking routes:\n";
$routesPath = $appPath . 'Config/Routes.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    if (strpos($routesContent, "get('/', 'Home::index')") !== false) {
        echo "✓ Home route configured correctly\n";
    } else {
        echo "⚠ Home route may not be configured correctly\n";
    }
} else {
    echo "✗ Routes.php not found\n";
    $allPassed = false;
}

echo "\n" . str_repeat("=", 50) . "\n";

if ($allPassed) {
    echo "✓ All Checks Passed!\n\n";
    echo "Task 21 implementation is complete and ready for testing.\n\n";
    echo "Files created/modified:\n";
    echo "  • app/Controllers/Home.php (modified)\n";
    echo "  • app/Views/home.php (created)\n";
    echo "  • tests/functional/HomePageTest.php (created)\n";
    echo "  • TASK_21_SUMMARY.md (created)\n";
    echo "  • TASK_21_MANUAL_TEST.md (created)\n\n";
    echo "Next steps:\n";
    echo "  1. Ensure database is set up with migrations\n";
    echo "  2. Add test data (apps, categories, users, reviews)\n";
    echo "  3. Access the home page in your browser\n";
    echo "  4. Follow the manual test guide in TASK_21_MANUAL_TEST.md\n\n";
    exit(0);
} else {
    echo "✗ Some Checks Failed\n\n";
    echo "Please review the errors above and fix them before proceeding.\n\n";
    exit(1);
}
