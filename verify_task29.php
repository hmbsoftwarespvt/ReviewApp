<?php

/**
 * Manual Verification Script for Task 29: Newsletter Subscription
 * 
 * This script tests the newsletter subscription functionality manually
 * using the actual MySQL database.
 */

// Define path constants
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('APPPATH', __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Load CodeIgniter
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsConfig = APPPATH . 'Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;

$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

// Get instances
$app->initialize();
$config = config('App');
$config->baseURL = 'http://localhost/';

use App\Models\NewsletterSubscriberModel;

$newsletterModel = new NewsletterSubscriberModel();

echo "=== Task 29: Newsletter Subscription Verification ===\n\n";

$testsPassed = 0;
$testsFailed = 0;

// Helper function to run tests
function runTest($testName, $callback) {
    global $testsPassed, $testsFailed;
    
    echo "Testing: $testName\n";
    
    try {
        $result = $callback();
        if ($result) {
            echo "  ✓ PASSED\n\n";
            $testsPassed++;
        } else {
            echo "  ✗ FAILED\n\n";
            $testsFailed++;
        }
    } catch (Exception $e) {
        echo "  ✗ FAILED: " . $e->getMessage() . "\n\n";
        $testsFailed++;
    }
}

// Test 1: NewsletterSubscriberModel exists and is accessible
runTest("NewsletterSubscriberModel exists", function() use ($newsletterModel) {
    return $newsletterModel instanceof NewsletterSubscriberModel;
});

// Test 2: Create a test subscription
runTest("Create newsletter subscription", function() use ($newsletterModel) {
    $email = 'test_' . time() . '@example.com';
    
    $data = [
        'email' => $email,
        'unsubscribe_token' => bin2hex(random_bytes(32)),
        'confirmation_token' => bin2hex(random_bytes(32)),
        'is_confirmed' => false,
        'subscribed_at' => date('Y-m-d H:i:s'),
    ];
    
    $id = $newsletterModel->insert($data);
    
    if (!$id) {
        echo "    Error: " . json_encode($newsletterModel->errors()) . "\n";
        return false;
    }
    
    $subscriber = $newsletterModel->find($id);
    return $subscriber && $subscriber['email'] === $email;
});

// Test 3: Email validation - duplicate prevention
runTest("Duplicate email prevention", function() use ($newsletterModel) {
    $email = 'duplicate_' . time() . '@example.com';
    
    // First subscription
    $data1 = [
        'email' => $email,
        'unsubscribe_token' => bin2hex(random_bytes(32)),
        'confirmation_token' => bin2hex(random_bytes(32)),
        'is_confirmed' => true,
        'subscribed_at' => date('Y-m-d H:i:s'),
    ];
    
    $id1 = $newsletterModel->insert($data1);
    
    if (!$id1) {
        return false;
    }
    
    // Try duplicate subscription
    $data2 = [
        'email' => $email,
        'unsubscribe_token' => bin2hex(random_bytes(32)),
        'confirmation_token' => bin2hex(random_bytes(32)),
        'is_confirmed' => false,
        'subscribed_at' => date('Y-m-d H:i:s'),
    ];
    
    $id2 = $newsletterModel->insert($data2);
    
    // Should fail due to unique constraint
    return $id2 === false;
});

// Test 4: Find subscriber by email
runTest("Find subscriber by email", function() use ($newsletterModel) {
    $email = 'findtest_' . time() . '@example.com';
    
    $data = [
        'email' => $email,
        'unsubscribe_token' => bin2hex(random_bytes(32)),
        'confirmation_token' => bin2hex(random_bytes(32)),
        'is_confirmed' => false,
        'subscribed_at' => date('Y-m-d H:i:s'),
    ];
    
    $id = $newsletterModel->insert($data);
    
    if (!$id) {
        return false;
    }
    
    $subscriber = $newsletterModel->findByEmail($email);
    return $subscriber && $subscriber['email'] === $email;
});

// Test 5: Find subscriber by unsubscribe token
runTest("Find subscriber by unsubscribe token", function() use ($newsletterModel) {
    $email = 'tokentest_' . time() . '@example.com';
    $token = bin2hex(random_bytes(32));
    
    $data = [
        'email' => $email,
        'unsubscribe_token' => $token,
        'confirmation_token' => bin2hex(random_bytes(32)),
        'is_confirmed' => false,
        'subscribed_at' => date('Y-m-d H:i:s'),
    ];
    
    $id = $newsletterModel->insert($data);
    
    if (!$id) {
        return false;
    }
    
    $subscriber = $newsletterModel->findByUnsubscribeToken($token);
    return $subscriber && $subscriber['unsubscribe_token'] === $token;
});

// Test 6: Confirm subscription
runTest("Confirm subscription", function() use ($newsletterModel) {
    $email = 'confirm_' . time() . '@example.com';
    
    $data = [
        'email' => $email,
        'unsubscribe_token' => bin2hex(random_bytes(32)),
        'confirmation_token' => bin2hex(random_bytes(32)),
        'is_confirmed' => false,
        'subscribed_at' => date('Y-m-d H:i:s'),
    ];
    
    $id = $newsletterModel->insert($data);
    
    if (!$id) {
        return false;
    }
    
    $success = $newsletterModel->confirmSubscription($id);
    
    if (!$success) {
        return false;
    }
    
    $subscriber = $newsletterModel->find($id);
    return $subscriber['is_confirmed'] == 1 && $subscriber['confirmation_token'] === null;
});

// Test 7: Unsubscribe
runTest("Unsubscribe functionality", function() use ($newsletterModel) {
    $email = 'unsub_' . time() . '@example.com';
    
    $data = [
        'email' => $email,
        'unsubscribe_token' => bin2hex(random_bytes(32)),
        'confirmation_token' => bin2hex(random_bytes(32)),
        'is_confirmed' => true,
        'subscribed_at' => date('Y-m-d H:i:s'),
    ];
    
    $id = $newsletterModel->insert($data);
    
    if (!$id) {
        return false;
    }
    
    $success = $newsletterModel->unsubscribe($id);
    
    if (!$success) {
        return false;
    }
    
    $subscriber = $newsletterModel->find($id);
    return $subscriber['unsubscribed_at'] !== null;
});

// Test 8: Get confirmed subscribers
runTest("Get confirmed subscribers", function() use ($newsletterModel) {
    $email = 'confirmed_' . time() . '@example.com';
    
    $data = [
        'email' => $email,
        'unsubscribe_token' => bin2hex(random_bytes(32)),
        'confirmation_token' => bin2hex(random_bytes(32)),
        'is_confirmed' => true,
        'subscribed_at' => date('Y-m-d H:i:s'),
    ];
    
    $id = $newsletterModel->insert($data);
    
    if (!$id) {
        return false;
    }
    
    $confirmed = $newsletterModel->getConfirmed();
    
    // Check if our subscriber is in the list
    $found = false;
    foreach ($confirmed as $subscriber) {
        if ($subscriber['email'] === $email) {
            $found = true;
            break;
        }
    }
    
    return $found;
});

// Test 9: Email count daily limit check
runTest("Email count daily limit check", function() use ($newsletterModel) {
    $email = 'limit_' . time() . '@example.com';
    
    $data = [
        'email' => $email,
        'unsubscribe_token' => bin2hex(random_bytes(32)),
        'confirmation_token' => bin2hex(random_bytes(32)),
        'is_confirmed' => true,
        'subscribed_at' => date('Y-m-d H:i:s'),
        'email_count_today' => 0,
        'last_email_date' => date('Y-m-d'),
    ];
    
    $id = $newsletterModel->insert($data);
    
    if (!$id) {
        return false;
    }
    
    // Should be able to receive email (count is 0)
    $canReceive = $newsletterModel->canReceiveEmail($id);
    
    if (!$canReceive) {
        return false;
    }
    
    // Increment count 5 times
    for ($i = 0; $i < 5; $i++) {
        $newsletterModel->incrementEmailCount($id);
    }
    
    // Should NOT be able to receive email (count is 5, limit reached)
    $canReceiveAfterLimit = $newsletterModel->canReceiveEmail($id);
    
    return !$canReceiveAfterLimit;
});

// Test 10: Token uniqueness
runTest("Token uniqueness", function() use ($newsletterModel) {
    $tokens = [];
    
    for ($i = 0; $i < 5; $i++) {
        $email = 'unique_' . $i . '_' . time() . '@example.com';
        
        $data = [
            'email' => $email,
            'unsubscribe_token' => bin2hex(random_bytes(32)),
            'confirmation_token' => bin2hex(random_bytes(32)),
            'is_confirmed' => false,
            'subscribed_at' => date('Y-m-d H:i:s'),
        ];
        
        $id = $newsletterModel->insert($data);
        
        if (!$id) {
            return false;
        }
        
        $subscriber = $newsletterModel->find($id);
        $tokens[] = $subscriber['unsubscribe_token'];
        $tokens[] = $subscriber['confirmation_token'];
    }
    
    // Check all tokens are unique
    $uniqueTokens = array_unique($tokens);
    return count($tokens) === count($uniqueTokens);
});

// Test 11: NewsletterController exists
runTest("NewsletterController exists", function() {
    $controllerPath = APPPATH . 'Controllers/NewsletterController.php';
    return file_exists($controllerPath);
});

// Test 12: Unsubscribe view exists
runTest("Unsubscribe view exists", function() {
    $viewPath = APPPATH . 'Views/newsletter/unsubscribe.php';
    return file_exists($viewPath);
});

// Test 13: Routes are configured
runTest("Newsletter routes configured", function() {
    $routesPath = APPPATH . 'Config/Routes.php';
    $routesContent = file_get_contents($routesPath);
    
    $hasSubscribeRoute = strpos($routesContent, 'newsletter/subscribe') !== false;
    $hasUnsubscribeRoute = strpos($routesContent, 'newsletter/unsubscribe') !== false;
    $hasConfirmRoute = strpos($routesContent, 'newsletter/confirm') !== false;
    
    return $hasSubscribeRoute && $hasUnsubscribeRoute && $hasConfirmRoute;
});

// Summary
echo "\n=== Test Summary ===\n";
echo "Tests Passed: $testsPassed\n";
echo "Tests Failed: $testsFailed\n";
echo "Total Tests: " . ($testsPassed + $testsFailed) . "\n";

if ($testsFailed === 0) {
    echo "\n✓ All tests passed! Task 29 implementation is complete.\n";
} else {
    echo "\n✗ Some tests failed. Please review the implementation.\n";
}

// Clean up test data
echo "\n=== Cleaning up test data ===\n";
$db = \Config\Database::connect();
$db->query("DELETE FROM newsletter_subscribers WHERE email LIKE 'test_%' OR email LIKE 'duplicate_%' OR email LIKE 'findtest_%' OR email LIKE 'tokentest_%' OR email LIKE 'confirm_%' OR email LIKE 'unsub_%' OR email LIKE 'confirmed_%' OR email LIKE 'limit_%' OR email LIKE 'unique_%'");
echo "Test data cleaned up.\n";

