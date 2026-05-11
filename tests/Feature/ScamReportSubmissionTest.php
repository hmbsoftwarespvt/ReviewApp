<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\AppModel;
use App\Models\UserModel;
use App\Models\ScamReportModel;
use App\Models\CategoryModel;

/**
 * Feature Tests for Task 28: Scam Report Submission
 * 
 * Tests all acceptance criteria:
 * - Authenticated users can submit scam reports
 * - Form validates description length (100-3000 chars) and evidence URL count (max 5)
 * - Risk level selection required
 * - Reports set to pending status
 * - Success message displayed
 */
class ScamReportSubmissionTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected $seed = 'TestSeeder';

    protected $basePath = 'tests/_support/Database';

    protected AppModel $appModel;
    protected UserModel $userModel;
    protected ScamReportModel $scamReportModel;
    protected CategoryModel $categoryModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->appModel = new AppModel();
        $this->userModel = new UserModel();
        $this->scamReportModel = new ScamReportModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Test: Authenticated users can submit scam reports
     */
    public function testAuthenticatedUserCanSubmitScamReport()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Simulate authenticated session
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = 'testuser';

        // Submit scam report
        $result = $this->withSession([
            'isLoggedIn' => true,
            'user_id' => $userId,
            'username' => 'testuser',
        ])->post("apps/submit-scam-report/{$appId}", [
            'title' => 'Suspicious behavior detected',
            'description' => str_repeat('This app exhibits suspicious behavior that may indicate a scam. ', 10), // 100+ chars
            'risk_level' => 'high',
            'evidence_url_1' => 'https://example.com/evidence1',
        ]);

        // Assert redirect with success message
        $result->assertRedirect();
        $result->assertSessionHas('success');

        // Verify scam report was created in database
        $report = $this->scamReportModel->where('app_id', $appId)
                                       ->where('user_id', $userId)
                                       ->first();

        $this->assertNotNull($report);
        $this->assertEquals('Suspicious behavior detected', $report['title']);
        $this->assertEquals('high', $report['risk_level']);
        $this->assertEquals('pending', $report['approval_status']);
    }

    /**
     * Test: Unauthenticated users cannot submit scam reports
     */
    public function testUnauthenticatedUserCannotSubmitScamReport()
    {
        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Attempt to submit scam report without authentication
        $result = $this->post("apps/submit-scam-report/{$appId}", [
            'title' => 'Suspicious behavior',
            'description' => str_repeat('This app exhibits suspicious behavior. ', 10),
            'risk_level' => 'high',
        ]);

        // Assert redirect to login
        $result->assertRedirect();
        $result->assertRedirectTo('/auth/login');
    }

    /**
     * Test: Description validation - minimum 100 characters
     */
    public function testDescriptionMinimumLengthValidation()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Submit scam report with description < 100 characters
        $result = $this->withSession([
            'isLoggedIn' => true,
            'user_id' => $userId,
            'username' => 'testuser',
        ])->post("apps/submit-scam-report/{$appId}", [
            'title' => 'Short description',
            'description' => 'Too short', // Less than 100 characters
            'risk_level' => 'high',
        ]);

        // Assert validation error
        $result->assertRedirect();
        $result->assertSessionHas('errors');

        // Verify no scam report was created
        $report = $this->scamReportModel->where('app_id', $appId)
                                       ->where('user_id', $userId)
                                       ->first();
        $this->assertNull($report);
    }

    /**
     * Test: Description validation - maximum 3000 characters
     */
    public function testDescriptionMaximumLengthValidation()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Submit scam report with description > 3000 characters
        $result = $this->withSession([
            'isLoggedIn' => true,
            'user_id' => $userId,
            'username' => 'testuser',
        ])->post("apps/submit-scam-report/{$appId}", [
            'title' => 'Long description',
            'description' => str_repeat('A', 3001), // More than 3000 characters
            'risk_level' => 'high',
        ]);

        // Assert validation error
        $result->assertRedirect();
        $result->assertSessionHas('errors');

        // Verify no scam report was created
        $report = $this->scamReportModel->where('app_id', $appId)
                                       ->where('user_id', $userId)
                                       ->first();
        $this->assertNull($report);
    }

    /**
     * Test: Risk level is required
     */
    public function testRiskLevelRequired()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Submit scam report without risk level
        $result = $this->withSession([
            'isLoggedIn' => true,
            'user_id' => $userId,
            'username' => 'testuser',
        ])->post("apps/submit-scam-report/{$appId}", [
            'title' => 'Missing risk level',
            'description' => str_repeat('This app exhibits suspicious behavior. ', 10),
            // risk_level is missing
        ]);

        // Assert validation error
        $result->assertRedirect();
        $result->assertSessionHas('errors');

        // Verify no scam report was created
        $report = $this->scamReportModel->where('app_id', $appId)
                                       ->where('user_id', $userId)
                                       ->first();
        $this->assertNull($report);
    }

    /**
     * Test: Evidence URLs validation - maximum 5 URLs
     */
    public function testEvidenceUrlsMaximumFive()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Submit scam report with 5 evidence URLs (should succeed)
        $result = $this->withSession([
            'isLoggedIn' => true,
            'user_id' => $userId,
            'username' => 'testuser',
        ])->post("apps/submit-scam-report/{$appId}", [
            'title' => 'Multiple evidence URLs',
            'description' => str_repeat('This app exhibits suspicious behavior. ', 10),
            'risk_level' => 'high',
            'evidence_url_1' => 'https://example.com/evidence1',
            'evidence_url_2' => 'https://example.com/evidence2',
            'evidence_url_3' => 'https://example.com/evidence3',
            'evidence_url_4' => 'https://example.com/evidence4',
            'evidence_url_5' => 'https://example.com/evidence5',
        ]);

        // Assert success
        $result->assertRedirect();
        $result->assertSessionHas('success');

        // Verify scam report was created with all evidence URLs
        $report = $this->scamReportModel->where('app_id', $appId)
                                       ->where('user_id', $userId)
                                       ->first();

        $this->assertNotNull($report);
        $evidenceUrls = json_decode($report['evidence_urls'], true);
        $this->assertCount(5, $evidenceUrls);
    }

    /**
     * Test: Reports are set to pending status
     */
    public function testReportsSetToPendingStatus()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Submit scam report
        $result = $this->withSession([
            'isLoggedIn' => true,
            'user_id' => $userId,
            'username' => 'testuser',
        ])->post("apps/submit-scam-report/{$appId}", [
            'title' => 'Test report',
            'description' => str_repeat('This app exhibits suspicious behavior. ', 10),
            'risk_level' => 'medium',
        ]);

        // Verify report has pending status
        $report = $this->scamReportModel->where('app_id', $appId)
                                       ->where('user_id', $userId)
                                       ->first();

        $this->assertNotNull($report);
        $this->assertEquals('pending', $report['approval_status']);
    }

    /**
     * Test: Success message displayed after submission
     */
    public function testSuccessMessageDisplayed()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Submit scam report
        $result = $this->withSession([
            'isLoggedIn' => true,
            'user_id' => $userId,
            'username' => 'testuser',
        ])->post("apps/submit-scam-report/{$appId}", [
            'title' => 'Test report',
            'description' => str_repeat('This app exhibits suspicious behavior. ', 10),
            'risk_level' => 'low',
        ]);

        // Assert success message in session
        $result->assertSessionHas('success');
        
        // Verify the success message contains expected text
        $session = session();
        $successMessage = $session->getFlashdata('success');
        $this->assertStringContainsString('submitted', $successMessage);
        $this->assertStringContainsString('pending verification', $successMessage);
    }

    /**
     * Test: Pending scam report indicator displayed
     */
    public function testPendingScamReportIndicatorDisplayed()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Create pending scam report
        $this->scamReportModel->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Pending report',
            'description' => str_repeat('This is a pending report. ', 10),
            'risk_level' => 'high',
            'approval_status' => 'pending',
        ]);

        // Visit app detail page
        $result = $this->withSession([
            'isLoggedIn' => true,
            'user_id' => $userId,
            'username' => 'testuser',
        ])->get("apps/test-app");

        // Assert page contains pending indicator
        $result->assertSee('Your scam report is pending verification');
        $result->assertSee('Pending report');
    }

    /**
     * Test: Invalid evidence URL format rejected
     */
    public function testInvalidEvidenceUrlRejected()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Submit scam report with invalid URL
        $result = $this->withSession([
            'isLoggedIn' => true,
            'user_id' => $userId,
            'username' => 'testuser',
        ])->post("apps/submit-scam-report/{$appId}", [
            'title' => 'Invalid URL',
            'description' => str_repeat('This app exhibits suspicious behavior. ', 10),
            'risk_level' => 'high',
            'evidence_url_1' => 'not-a-valid-url',
        ]);

        // Assert validation error
        $result->assertRedirect();
        $result->assertSessionHas('errors');

        // Verify no scam report was created
        $report = $this->scamReportModel->where('app_id', $appId)
                                       ->where('user_id', $userId)
                                       ->first();
        $this->assertNull($report);
    }

    /**
     * Test: All risk levels (low, medium, high) accepted
     */
    public function testAllRiskLevelsAccepted()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test apps
        $appIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $appIds[] = $this->appModel->insert([
                'name' => "Test App {$i}",
                'slug' => "test-app-{$i}",
                'description' => 'Test app description',
                'platform_type' => 'android',
                'developer_name' => 'Test Developer',
                'approval_status' => 'approved',
            ]);
        }

        $riskLevels = ['low', 'medium', 'high'];

        foreach ($riskLevels as $index => $riskLevel) {
            // Submit scam report with each risk level
            $result = $this->withSession([
                'isLoggedIn' => true,
                'user_id' => $userId,
                'username' => 'testuser',
            ])->post("apps/submit-scam-report/{$appIds[$index]}", [
                'title' => "Report with {$riskLevel} risk",
                'description' => str_repeat('This app exhibits suspicious behavior. ', 10),
                'risk_level' => $riskLevel,
            ]);

            // Assert success
            $result->assertRedirect();
            $result->assertSessionHas('success');

            // Verify report was created with correct risk level
            $report = $this->scamReportModel->where('app_id', $appIds[$index])
                                           ->where('user_id', $userId)
                                           ->first();

            $this->assertNotNull($report);
            $this->assertEquals($riskLevel, $report['risk_level']);
        }
    }
}
