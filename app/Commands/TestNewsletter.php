<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\NewsletterSubscriberModel;

class TestNewsletter extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:newsletter';
    protected $description = 'Test newsletter subscription functionality for Task 29';

    public function run(array $params)
    {
        CLI::write('=== Task 29: Newsletter Subscription Verification ===', 'yellow');
        CLI::newLine();

        $newsletterModel = new NewsletterSubscriberModel();
        $testsPassed = 0;
        $testsFailed = 0;

        // Test 1: Model exists
        CLI::write('Test 1: NewsletterSubscriberModel exists');
        if ($newsletterModel instanceof NewsletterSubscriberModel) {
            CLI::write('  ✓ PASSED', 'green');
            $testsPassed++;
        } else {
            CLI::write('  ✗ FAILED', 'red');
            $testsFailed++;
        }
        CLI::newLine();

        // Test 2: Create subscription
        CLI::write('Test 2: Create newsletter subscription');
        $email = 'test_' . time() . '@example.com';
        $data = [
            'email' => $email,
            'unsubscribe_token' => bin2hex(random_bytes(32)),
            'confirmation_token' => bin2hex(random_bytes(32)),
            'is_confirmed' => false,
            'subscribed_at' => date('Y-m-d H:i:s'),
        ];
        
        $id = $newsletterModel->insert($data);
        if ($id) {
            CLI::write('  ✓ PASSED - Created subscriber ID: ' . $id, 'green');
            $testsPassed++;
        } else {
            CLI::write('  ✗ FAILED - ' . json_encode($newsletterModel->errors()), 'red');
            $testsFailed++;
        }
        CLI::newLine();

        // Test 3: Find by email
        CLI::write('Test 3: Find subscriber by email');
        $subscriber = $newsletterModel->findByEmail($email);
        if ($subscriber && $subscriber['email'] === $email) {
            CLI::write('  ✓ PASSED', 'green');
            $testsPassed++;
        } else {
            CLI::write('  ✗ FAILED', 'red');
            $testsFailed++;
        }
        CLI::newLine();

        // Test 4: Confirm subscription
        CLI::write('Test 4: Confirm subscription');
        $success = $newsletterModel->confirmSubscription($id);
        $confirmedSubscriber = $newsletterModel->find($id);
        if ($success && $confirmedSubscriber['is_confirmed'] == 1) {
            CLI::write('  ✓ PASSED', 'green');
            $testsPassed++;
        } else {
            CLI::write('  ✗ FAILED', 'red');
            $testsFailed++;
        }
        CLI::newLine();

        // Test 5: Unsubscribe
        CLI::write('Test 5: Unsubscribe functionality');
        $success = $newsletterModel->unsubscribe($id);
        $unsubscribedSubscriber = $newsletterModel->find($id);
        if ($success && $unsubscribedSubscriber['unsubscribed_at'] !== null) {
            CLI::write('  ✓ PASSED', 'green');
            $testsPassed++;
        } else {
            CLI::write('  ✗ FAILED', 'red');
            $testsFailed++;
        }
        CLI::newLine();

        // Test 6: Controller exists
        CLI::write('Test 6: NewsletterController exists');
        $controllerPath = APPPATH . 'Controllers/NewsletterController.php';
        if (file_exists($controllerPath)) {
            CLI::write('  ✓ PASSED', 'green');
            $testsPassed++;
        } else {
            CLI::write('  ✗ FAILED', 'red');
            $testsFailed++;
        }
        CLI::newLine();

        // Test 7: Unsubscribe view exists
        CLI::write('Test 7: Unsubscribe view exists');
        $viewPath = APPPATH . 'Views/newsletter/unsubscribe.php';
        if (file_exists($viewPath)) {
            CLI::write('  ✓ PASSED', 'green');
            $testsPassed++;
        } else {
            CLI::write('  ✗ FAILED', 'red');
            $testsFailed++;
        }
        CLI::newLine();

        // Test 8: Routes configured
        CLI::write('Test 8: Newsletter routes configured');
        $routesPath = APPPATH . 'Config/Routes.php';
        $routesContent = file_get_contents($routesPath);
        $hasSubscribeRoute = strpos($routesContent, 'newsletter/subscribe') !== false;
        $hasUnsubscribeRoute = strpos($routesContent, 'newsletter/unsubscribe') !== false;
        $hasConfirmRoute = strpos($routesContent, 'newsletter/confirm') !== false;
        
        if ($hasSubscribeRoute && $hasUnsubscribeRoute && $hasConfirmRoute) {
            CLI::write('  ✓ PASSED', 'green');
            $testsPassed++;
        } else {
            CLI::write('  ✗ FAILED', 'red');
            $testsFailed++;
        }
        CLI::newLine();

        // Clean up test data
        CLI::write('Cleaning up test data...', 'yellow');
        $db = \Config\Database::connect();
        $db->query("DELETE FROM newsletter_subscribers WHERE email LIKE 'test_%'");
        CLI::write('Test data cleaned up.', 'green');
        CLI::newLine();

        // Summary
        CLI::write('=== Test Summary ===', 'yellow');
        CLI::write('Tests Passed: ' . $testsPassed, 'green');
        CLI::write('Tests Failed: ' . $testsFailed, ($testsFailed > 0 ? 'red' : 'green'));
        CLI::write('Total Tests: ' . ($testsPassed + $testsFailed));
        CLI::newLine();

        if ($testsFailed === 0) {
            CLI::write('✓ All tests passed! Task 29 implementation is complete.', 'green');
        } else {
            CLI::write('✗ Some tests failed. Please review the implementation.', 'red');
        }
    }
}

