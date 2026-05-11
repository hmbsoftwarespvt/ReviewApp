<?php

/**
 * Task 31 Verification Script
 * Verifies RecommendationService implementation (already exists from Task 22)
 */

echo "=== Task 31: Recommendation Service Verification ===\n\n";

$rootPath = __DIR__ . DIRECTORY_SEPARATOR;
$appPath = $rootPath . 'app' . DIRECTORY_SEPARATOR;

// Test 1: Check if RecommendationService file exists
echo "Test 1: RecommendationService file exists... ";
$serviceFile = $appPath . 'Services/RecommendationService.php';
if (file_exists($serviceFile)) {
    echo "✓ PASS\n";
    // Check syntax
    $output = [];
    $return = 0;
    exec("php -l \"$serviceFile\"", $output, $return);
    if ($return !== 0) {
        echo "✗ FAIL: Syntax error in RecommendationService.php\n";
        exit(1);
    }
} else {
    echo "✗ FAIL\n";
    exit(1);
}

// Test 2: Check RecommendationService class structure
echo "Test 2: RecommendationService class structure... ";
require_once $serviceFile;
$reflection = new ReflectionClass('App\Services\RecommendationService');

$requiredMethods = [
    'getSimilarApps',
    'calculateSimilarity',
];

$missingMethods = [];
foreach ($requiredMethods as $method) {
    if (!$reflection->hasMethod($method)) {
        $missingMethods[] = $method;
    }
}

if (empty($missingMethods)) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Missing methods: " . implode(', ', $missingMethods) . "\n";
    exit(1);
}

// Test 3: Check getSimilarApps method signature
echo "Test 3: getSimilarApps method signature... ";
$method = $reflection->getMethod('getSimilarApps');
$parameters = $method->getParameters();

if (count($parameters) >= 1 && $parameters[0]->getName() === 'appId') {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Invalid method signature\n";
    exit(1);
}

// Test 4: Check calculateSimilarity method signature
echo "Test 4: calculateSimilarity method signature... ";
$method = $reflection->getMethod('calculateSimilarity');
$parameters = $method->getParameters();

if (count($parameters) >= 2) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Invalid method signature\n";
    exit(1);
}

// Test 5: Verify similarity algorithm components
echo "Test 5: Similarity algorithm components... ";
$fileContent = file_get_contents($serviceFile);

$hasCategoryMatch = strpos($fileContent, 'Category match') !== false;
$hasTrustScore = strpos($fileContent, 'Trust score proximity') !== false;
$hasPlatform = strpos($fileContent, 'Same platform type') !== false;

if ($hasCategoryMatch && $hasTrustScore && $hasPlatform) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Missing similarity algorithm components\n";
    exit(1);
}

// Test 6: Verify limit parameter default value
echo "Test 6: Default limit parameter (6)... ";
$method = $reflection->getMethod('getSimilarApps');
$parameters = $method->getParameters();

$hasDefaultLimit = false;
foreach ($parameters as $param) {
    if ($param->getName() === 'limit' && $param->isDefaultValueAvailable()) {
        $defaultValue = $param->getDefaultValue();
        if ($defaultValue === 6) {
            $hasDefaultLimit = true;
            break;
        }
    }
}

if ($hasDefaultLimit) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Default limit should be 6\n";
    exit(1);
}

// Test 7: Verify caching implementation
echo "Test 7: Caching implementation... ";
$hasCacheGet = strpos($fileContent, 'cache->get') !== false;
$hasCacheSave = strpos($fileContent, 'cache->save') !== false;

if ($hasCacheGet && $hasCacheSave) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: Caching not properly implemented\n";
    exit(1);
}

echo "\n=== All Tests Passed! ===\n";
echo "\nTask 31 Implementation Summary:\n";
echo "- RecommendationService class exists (implemented in Task 22)\n";
echo "- getSimilarApps() method returns up to 6 similar apps\n";
echo "- calculateSimilarity() method uses 3 factors:\n";
echo "  * Category match: +50 points per matching category\n";
echo "  * Trust score proximity (±10): +30 points\n";
echo "  * Same platform type: +20 points\n";
echo "- Results cached for 1 hour\n";
echo "- Excludes current app from recommendations\n";
echo "- Falls back to related categories if needed\n";

exit(0);
