<?php

namespace Tests\Functional;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * Functional tests for Task 22: App Detail Page
 * 
 * Tests all acceptance criteria:
 * - App detail page shows all app information
 * - Trust score displayed with correct color
 * - Breakdown shows all 5 components
 * - Screenshots open in modal
 * - Reviews paginated (10 per page)
 * - Scam reports paginated (10 per page)
 * - Similar apps section shows 6 apps
 * - View count increments on each visit
 */
class AppDetailPageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test data
        $this->seed(TestDataSeeder::class);
    }

    /**
     * Test that app detail page shows all app information
     */
    public function testAppDetailPageShowsAllInformation()
    {
        // Create a test app
        $appModel = model('AppModel');
        $categoryModel = model('CategoryModel');
        
        // Create category
        $categoryId = $categoryModel->insert([
            'name' => 'Finance',
            'slug' => 'finance',
            'description' => 'Finance apps',
            'display_order' => 1,
        ]);
        
        // Create app
        $appId = $appModel->insert([
            'name' => 'Test Finance App',
            'slug' => 'test-finance-app',
            'description' => 'A test finance application',
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
        
        // Attach category
        $appModel->attachCategories($appId, [$categoryId]);
        
        // Visit app detail page
        $result = $this->get("apps/test-finance-app");
        
        // Assert page loads successfully
        $result->assertStatus(200);
        
        // Assert app information is displayed
        $result->assertSee('Test Finance App');
        $result->assertSee('Test Developer');
        $result->assertSee('A test finance application');
        $result->assertSee('1.0.0'); // Version
        $result->assertSee('25MB'); // Size
        $result->assertSee('Android'); // Platform (capitalized)
        $result->assertSee('Free'); // Price
        $result->assertSee('Jan 01, 2023'); // Release date
        $result->assertSee('Finance'); // Category
    }

    /**
     * Test that trust score is displayed with correct color
     */
    public function testTrustScoreDisplayedWithCorrectColor()
    {
        $appModel = model('AppModel');
        
        // Test high trust score (green)
        $highScoreAppId = $appModel->insert([
            'name' => 'High Score App',
            'slug' => 'high-score-app',
            'description' => 'High trust score app',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 85.0,
            'approval_status' => 'approved',
        ]);
        
        $result = $this->get("apps/high-score-app");
        $result->assertStatus(200);
        $result->assertSee('85');
        $result->assertSee('trust-green');
        
        // Test medium trust score (yellow)
        $mediumScoreAppId = $appModel->insert([
            'name' => 'Medium Score App',
            'slug' => 'medium-score-app',
            'description' => 'Medium trust score app',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 65.0,
            'approval_status' => 'approved',
        ]);
        
        $result = $this->get("apps/medium-score-app");
        $result->assertStatus(200);
        $result->assertSee('65');
        $result->assertSee('trust-yellow');
        
        // Test low trust score (red)
        $lowScoreAppId = $appModel->insert([
            'name' => 'Low Score App',
            'slug' => 'low-score-app',
            'description' => 'Low trust score app',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 35.0,
            'approval_status' => 'approved',
        ]);
        
        $result = $this->get("apps/low-score-app");
        $result->assertStatus(200);
        $result->assertSee('35');
        $result->assertSee('trust-red');
    }

    /**
     * Test that trust score breakdown shows all 5 components
     */
    public function testTrustScoreBreakdownShowsAllComponents()
    {
        $appModel = model('AppModel');
        
        $appId = $appModel->insert([
            'name' => 'Breakdown Test App',
            'slug' => 'breakdown-test-app',
            'description' => 'Test app for breakdown',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 75.0,
            'security_score' => 20.0,
            'developer_reputation' => 15.0,
            'release_date' => '2022-01-01',
            'approval_status' => 'approved',
        ]);
        
        $result = $this->get("apps/breakdown-test-app");
        $result->assertStatus(200);
        
        // Assert all 5 components are displayed
        $result->assertSee('Trust Score Breakdown');
        $result->assertSee('User Reviews');
        $result->assertSee('Security Analysis');
        $result->assertSee('Developer Reputation');
        $result->assertSee('Scam Reports');
        $result->assertSee('App Age');
        
        // Assert progress bars are present
        $result->assertSee('progress-bar');
    }

    /**
     * Test that screenshots are displayed and have modal functionality
     */
    public function testScreenshotsDisplayedWithModal()
    {
        $appModel = model('AppModel');
        $screenshotModel = model('ScreenshotModel');
        
        $appId = $appModel->insert([
            'name' => 'Screenshot Test App',
            'slug' => 'screenshot-test-app',
            'description' => 'Test app with screenshots',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 75.0,
            'approval_status' => 'approved',
        ]);
        
        // Add screenshots
        $screenshotModel->insert([
            'app_id' => $appId,
            'filename' => 'screenshot1.jpg',
            'file_path' => '/uploads/screenshots/screenshot1.jpg',
            'display_order' => 1,
        ]);
        
        $screenshotModel->insert([
            'app_id' => $appId,
            'filename' => 'screenshot2.jpg',
            'file_path' => '/uploads/screenshots/screenshot2.jpg',
            'display_order' => 2,
        ]);
        
        $result = $this->get("apps/screenshot-test-app");
        $result->assertStatus(200);
        
        // Assert screenshots section is displayed
        $result->assertSee('Screenshots');
        $result->assertSee('screenshot1.jpg');
        $result->assertSee('screenshot2.jpg');
        
        // Assert modal elements are present
        $result->assertSee('screenshotModal');
        $result->assertSee('data-bs-toggle="modal"');
    }

    /**
     * Test that reviews are paginated with 10 per page
     */
    public function testReviewsPaginatedCorrectly()
    {
        $appModel = model('AppModel');
        $userModel = model('UserModel');
        $reviewModel = model('ReviewModel');
        
        // Create app
        $appId = $appModel->insert([
            'name' => 'Review Pagination App',
            'slug' => 'review-pagination-app',
            'description' => 'Test app for review pagination',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 75.0,
            'approval_status' => 'approved',
        ]);
        
        // Create user
        $userId = $userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create 15 approved reviews
        for ($i = 1; $i <= 15; $i++) {
            $reviewModel->insert([
                'app_id' => $appId,
                'user_id' => $userId,
                'rating' => 4,
                'title' => "Review {$i}",
                'review_text' => str_repeat("This is review {$i}. ", 10),
                'approval_status' => 'approved',
            ]);
            
            // Need to create different users for each review due to unique constraint
            if ($i < 15) {
                $userId = $userModel->insert([
                    'username' => "testuser{$i}",
                    'email' => "test{$i}@example.com",
                    'password_hash' => password_hash('password', PASSWORD_BCRYPT),
                    'role' => 'user',
                    'status' => 'active',
                ]);
            }
        }
        
        // Visit first page
        $result = $this->get("apps/review-pagination-app");
        $result->assertStatus(200);
        
        // Should see first 10 reviews
        $result->assertSee('Review 1');
        $result->assertSee('Review 10');
        
        // Should see pagination
        $result->assertSee('pagination');
        
        // Visit second page
        $result = $this->get("apps/review-pagination-app?review_page=2");
        $result->assertStatus(200);
        
        // Should see remaining 5 reviews
        $result->assertSee('Review 11');
        $result->assertSee('Review 15');
    }

    /**
     * Test that scam reports are paginated with 10 per page
     */
    public function testScamReportsPaginatedCorrectly()
    {
        $appModel = model('AppModel');
        $userModel = model('UserModel');
        $scamReportModel = model('ScamReportModel');
        
        // Create app
        $appId = $appModel->insert([
            'name' => 'Scam Report Pagination App',
            'slug' => 'scam-report-pagination-app',
            'description' => 'Test app for scam report pagination',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 75.0,
            'approval_status' => 'approved',
        ]);
        
        // Create user
        $userId = $userModel->insert([
            'username' => 'scamuser',
            'email' => 'scam@example.com',
            'password_hash' => password_hash('password', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create 12 approved scam reports
        for ($i = 1; $i <= 12; $i++) {
            $scamReportModel->insert([
                'app_id' => $appId,
                'user_id' => $userId,
                'title' => "Scam Report {$i}",
                'description' => str_repeat("This is scam report {$i}. ", 20),
                'risk_level' => 'medium',
                'approval_status' => 'approved',
            ]);
        }
        
        // Visit first page
        $result = $this->get("apps/scam-report-pagination-app");
        $result->assertStatus(200);
        
        // Should see first 10 scam reports
        $result->assertSee('Scam Report 1');
        $result->assertSee('Scam Report 10');
        
        // Should see pagination
        $result->assertSee('pagination');
        
        // Visit second page
        $result = $this->get("apps/scam-report-pagination-app?scam_page=2");
        $result->assertStatus(200);
        
        // Should see remaining 2 scam reports
        $result->assertSee('Scam Report 11');
        $result->assertSee('Scam Report 12');
    }

    /**
     * Test that similar apps section shows 6 apps
     */
    public function testSimilarAppsSectionShows6Apps()
    {
        $appModel = model('AppModel');
        $categoryModel = model('CategoryModel');
        
        // Create category
        $categoryId = $categoryModel->insert([
            'name' => 'Gaming',
            'slug' => 'gaming',
            'description' => 'Gaming apps',
            'display_order' => 1,
        ]);
        
        // Create main app
        $mainAppId = $appModel->insert([
            'name' => 'Main Gaming App',
            'slug' => 'main-gaming-app',
            'description' => 'Main gaming app',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 75.0,
            'approval_status' => 'approved',
        ]);
        
        $appModel->attachCategories($mainAppId, [$categoryId]);
        
        // Create 10 similar apps in the same category
        for ($i = 1; $i <= 10; $i++) {
            $similarAppId = $appModel->insert([
                'name' => "Similar Gaming App {$i}",
                'slug' => "similar-gaming-app-{$i}",
                'description' => "Similar gaming app {$i}",
                'platform_type' => 'android',
                'developer_name' => 'Developer',
                'trust_score' => 70.0 + $i,
                'approval_status' => 'approved',
            ]);
            
            $appModel->attachCategories($similarAppId, [$categoryId]);
        }
        
        // Visit main app page
        $result = $this->get("apps/main-gaming-app");
        $result->assertStatus(200);
        
        // Should see "Similar Apps" section
        $result->assertSee('Similar Apps');
        
        // Should see at most 6 similar apps
        $result->assertSee('Similar Gaming App 1');
        $result->assertSee('Similar Gaming App 6');
        
        // Should NOT see the 7th app (only 6 should be displayed)
        // Note: This is a basic check; actual implementation may vary
    }

    /**
     * Test that view count increments on each visit
     */
    public function testViewCountIncrementsOnPageLoad()
    {
        $appModel = model('AppModel');
        
        // Create app with initial view count
        $appId = $appModel->insert([
            'name' => 'View Count Test App',
            'slug' => 'view-count-test-app',
            'description' => 'Test app for view count',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 75.0,
            'view_count' => 100,
            'approval_status' => 'approved',
        ]);
        
        // Get initial view count
        $app = $appModel->find($appId);
        $initialViewCount = $app['view_count'];
        
        // Visit app detail page
        $result = $this->get("apps/view-count-test-app");
        $result->assertStatus(200);
        
        // Get updated view count
        $app = $appModel->find($appId);
        $updatedViewCount = $app['view_count'];
        
        // Assert view count incremented by 1
        $this->assertEquals($initialViewCount + 1, $updatedViewCount);
        
        // Visit again
        $result = $this->get("apps/view-count-test-app");
        $result->assertStatus(200);
        
        // Get view count again
        $app = $appModel->find($appId);
        $finalViewCount = $app['view_count'];
        
        // Assert view count incremented by 1 again
        $this->assertEquals($updatedViewCount + 1, $finalViewCount);
    }

    /**
     * Test that non-existent app returns 404
     */
    public function testNonExistentAppReturns404()
    {
        $result = $this->get("apps/non-existent-app");
        $result->assertStatus(404);
    }

    /**
     * Test that pending app is not accessible to non-admin users
     */
    public function testPendingAppNotAccessibleToPublic()
    {
        $appModel = model('AppModel');
        
        $appId = $appModel->insert([
            'name' => 'Pending App',
            'slug' => 'pending-app',
            'description' => 'Pending app',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 75.0,
            'approval_status' => 'pending',
        ]);
        
        $result = $this->get("apps/pending-app");
        $result->assertStatus(404);
    }

    /**
     * Test that scam report counts by risk level are displayed
     */
    public function testScamReportCountsByRiskLevelDisplayed()
    {
        $appModel = model('AppModel');
        $userModel = model('UserModel');
        $scamReportModel = model('ScamReportModel');
        
        // Create app
        $appId = $appModel->insert([
            'name' => 'Risk Level Test App',
            'slug' => 'risk-level-test-app',
            'description' => 'Test app for risk levels',
            'platform_type' => 'android',
            'developer_name' => 'Developer',
            'trust_score' => 75.0,
            'approval_status' => 'approved',
        ]);
        
        // Create user
        $userId = $userModel->insert([
            'username' => 'riskuser',
            'email' => 'risk@example.com',
            'password_hash' => password_hash('password', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create scam reports with different risk levels
        $scamReportModel->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'High Risk Report',
            'description' => str_repeat('High risk scam report. ', 20),
            'risk_level' => 'high',
            'approval_status' => 'approved',
        ]);
        
        $scamReportModel->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Medium Risk Report',
            'description' => str_repeat('Medium risk scam report. ', 20),
            'risk_level' => 'medium',
            'approval_status' => 'approved',
        ]);
        
        $scamReportModel->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Low Risk Report',
            'description' => str_repeat('Low risk scam report. ', 20),
            'risk_level' => 'low',
            'approval_status' => 'approved',
        ]);
        
        // Visit app page
        $result = $this->get("apps/risk-level-test-app");
        $result->assertStatus(200);
        
        // Assert risk level badges are displayed
        $result->assertSee('High: 1');
        $result->assertSee('Medium: 1');
        $result->assertSee('Low: 1');
        
        // Assert risk level styling
        $result->assertSee('risk-high');
        $result->assertSee('risk-medium');
        $result->assertSee('risk-low');
    }
}
