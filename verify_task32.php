<?php

/**
 * Task 32 Verification Script
 * Verifies App Comparison Tool implementation
 */

echo "=== Task 32: App Comparison Tool Verification ===\n\n";

$rootPath = __DIR__ . DIRECTORY_SEPARATOR;
$appPath = $rootPath . 'app' . DIRECTORY_SEPARATOR;

// Test 1: Check if Comparison controller file exists
echo "Test 1: Comparison controller file exists... ";
$controllerFile = $appPath . 'Controllers/Comparison.php';
if (file_exists($controllerFile)) {
    echo "✓ PASS\n";
    // Check syntax
    $output = [];
    $return = 0;
    exec("php -l \"$controllerFile\"", $output, $return);
    if ($return !== 0) {
        echo "✗ FAIL: Syntax error in Comparison.php\n";
        exit(1);
    }
} else {
    echo "✗ FAIL\n";
    exit(1);
}

// Test 2: Check if comparison view file exists
echo "Test 2: Comparison view file exists... ";
$viewFile = $appPath . 'Views/comparison/index.php';
if (file_exists($viewFile)) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL\n";
    exit(1);
}

// Test 3: Check Comparison controller structure
echo "Test 3: Comparison controller structure... ";
$controllerContent = file_get_contents($controllerFile);

$requiredMethods = [
    'public function index',
    'public function add',
    'public function remove',
    'public function clear',
    'public function search',
];

$missingMethods = [];
foreach ($requiredMethods as $method) {
    if (strpos($controllerContent, $method) === false) {
        $missingMethods[] = $method;
    }
}

if (empty($missingMethods)) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Missing methods: " . implode(', ', $missingMethods) . "\n";
    exit(1);
}

// Test 4: Check routes configuration
echo "Test 4: Routes configuration... ";
$routesFile = $appPath . 'Config/Routes.php';
$routesContent = file_get_contents($routesFile);

$hasComparisonRoute = strpos($routesContent, 'comparison') !== false;
$hasAddRoute = strpos($routesContent, 'comparison/add') !== false;
$hasRemoveRoute = strpos($routesContent, 'comparison/remove') !== false;
$hasClearRoute = strpos($routesContent, 'comparison/clear') !== false;
$hasSearchRoute = strpos($routesContent, 'comparison/search') !== false;

if ($hasComparisonRoute && $hasAddRoute && $hasRemoveRoute && $hasClearRoute && $hasSearchRoute) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Missing routes\n";
    exit(1);
}

// Test 5: Check view content
echo "Test 5: View content structure... ";
$viewContent = file_get_contents($viewFile);

$hasTable = strpos($viewContent, '<table') !== false;
$hasAddForm = strpos($viewContent, 'comparison/add') !== false;
$hasRemoveLink = strpos($viewContent, 'comparison/remove') !== false;
$hasClearLink = strpos($viewContent, 'comparison/clear') !== false;
$hasSearchInput = strpos($viewContent, 'appSearch') !== false;

if ($hasTable && $hasAddForm && $hasRemoveLink && $hasClearLink && $hasSearchInput) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Missing view elements\n";
    exit(1);
}

// Test 6: Check for trust score highlighting
echo "Test 6: Trust score highlighting... ";
$hasHighestHighlight = strpos($viewContent, 'highestScore') !== false;
$hasLowestHighlight = strpos($viewContent, 'lowestScore') !== false;
$hasColorClass = strpos($viewContent, 'table-success') !== false || strpos($viewContent, 'table-danger') !== false;

if ($hasHighestHighlight && $hasLowestHighlight && $hasColorClass) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Missing trust score highlighting\n";
    exit(1);
}

// Test 7: Check for session storage
echo "Test 7: Session storage implementation... ";

$hasSessionGet = strpos($controllerContent, "session()->get('comparison_apps')") !== false;
$hasSessionSet = strpos($controllerContent, "session()->set('comparison_apps'") !== false;
$hasSessionRemove = strpos($controllerContent, "session()->remove('comparison_apps')") !== false;

if ($hasSessionGet && $hasSessionSet && $hasSessionRemove) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Session storage not properly implemented\n";
    exit(1);
}

// Test 8: Check for 2-4 app limit
echo "Test 8: App limit validation (2-4 apps)... ";
$hasMinCheck = strpos($controllerContent, '>= 2') !== false;
$hasMaxCheck = strpos($controllerContent, '>= 4') !== false || strpos($controllerContent, '< 4') !== false;

if ($hasMinCheck && $hasMaxCheck) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: App limit validation missing\n";
    exit(1);
}

// Test 9: Check for trust score breakdown display
echo "Test 9: Trust score breakdown display... ";
$hasBreakdown = strpos($viewContent, 'breakdown') !== false;
$hasReviewRating = strpos($viewContent, 'review_rating') !== false;
$hasSecurityScore = strpos($viewContent, 'security_score') !== false;
$hasDeveloperReputation = strpos($viewContent, 'developer_reputation') !== false;
$hasScamReportCount = strpos($viewContent, 'scam_report_count') !== false;
$hasAppAge = strpos($viewContent, 'app_age') !== false;

if ($hasBreakdown && $hasReviewRating && $hasSecurityScore && $hasDeveloperReputation && $hasScamReportCount && $hasAppAge) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Trust score breakdown not complete\n";
    exit(1);
}

// Test 10: Check for AJAX search functionality
echo "Test 10: AJAX search functionality... ";
$hasSearchScript = strpos($viewContent, 'fetch') !== false;
$hasSearchEndpoint = strpos($viewContent, 'comparison/search') !== false;

if ($hasSearchScript && $hasSearchEndpoint) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: AJAX search not implemented\n";
    exit(1);
}

echo "\n=== All Tests Passed! ===\n";
echo "\nTask 32 Implementation Summary:\n";
echo "- ComparisonController created with all required methods\n";
echo "- Comparison view with side-by-side table\n";
echo "- App selection (2-4 apps) with validation\n";
echo "- Trust score, breakdown, ratings, reviews, scam reports displayed\n";
echo "- Highest/lowest trust scores highlighted (green/red)\n";
echo "- Selections stored in session\n";
echo "- AJAX search for adding apps\n";
echo "- Routes configured for all comparison actions\n";

exit(0);
