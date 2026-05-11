<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * ScamAlertsPageTest
 * 
 * Functional tests for Task 25: Scam Alerts Page
 * 
 * Tests:
 * - Scam alerts page shows all approved reports
 * - Filters work correctly (category, risk level, status)
 * - Risk levels color-coded (red=high, orange=medium, yellow=low)
 * - Reports sorted by date (descending)
 * - Links to app detail pages work
 * - Pagination (20 per page)
 */
class ScamAlertsPageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;
    protected $seed        = TestDataSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test: Scam alerts page shows all approved reports
     * 
     * Validates Requirement 11.1: Scam Alerts page displays all approved scam reports
     */
    public function testScamAlertsPageShowsAllApprovedReports(): void
    {
        $db = \Config\Database::connect();
        
        // Create test user
        $userId = $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create test app
        $appId = $db->table('apps')->insert([
            'name' => 'Scam App',
            'slug' => 'scam-app',
            'description' => 'A suspicious application',
            'platform_type' => 'android',
            'developer_name' => 'Scammer Inc',
            'trust_score' => 25.0,
            'approval_status' => 'approved',
        ]);
        
        // Create approved scam report
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Steals Personal Data',
            'description' => 'This app requests excessive permissions and appears to steal personal data without user consent. Multiple users have reported unauthorized charges.',
            'risk_level' => 'high',
            'evidence_urls' => json_encode(['https://example.com/evidence1']),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Create pending scam report (should not be shown)
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Pending Report',
            'description' => 'This is a pending report that should not be displayed on the public scam alerts page because it has not been verified yet.',
            'risk_level' => 'medium',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Visit scam alerts page
        $result = $this->get('scam-alerts');
        
        // Assert successful response
        $result->assertStatus(200);
        $result->assertSee('Scam Alerts');
        
        // Assert approved report is shown
        $result->assertSee('Steals Personal Data');
        $result->assertSee('Scam App');
        $result->assertSee('testuser');
        
        // Assert pending report is NOT shown
        $result->assertDontSee('Pending Report');
    }

    /**
     * Test: Risk level filter works correctly
     * 
     * Validates Requirement 11.3: Filtering by risk level
     */
    public function testRiskLevelFilterWorksCorrectly(): void
    {
        $db = \Config\Database::connect();
        
        // Create test user
        $userId = $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create test app
        $appId = $db->table('apps')->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test application',
            'platform_type' => 'android',
            'developer_name' => 'Test Dev',
            'trust_score' => 50.0,
            'approval_status' => 'approved',
        ]);
        
        // Create scam reports with different risk levels
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'High Risk Report',
            'description' => 'This is a high risk scam report that describes serious security vulnerabilities and data theft issues with this application.',
            'risk_level' => 'high',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Medium Risk Report',
            'description' => 'This is a medium risk scam report that describes moderate security concerns and potential privacy issues with this application.',
            'risk_level' => 'medium',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Low Risk Report',
            'description' => 'This is a low risk scam report that describes minor concerns and potential issues that users should be aware of with this app.',
            'risk_level' => 'low',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Test high risk filter
        $result = $this->get('scam-alerts?risk_level=high');
        $result->assertStatus(200);
        $result->assertSee('High Risk Report');
        $result->assertDontSee('Medium Risk Report');
        $result->assertDontSee('Low Risk Report');
        
        // Test medium risk filter
        $result = $this->get('scam-alerts?risk_level=medium');
        $result->assertStatus(200);
        $result->assertSee('Medium Risk Report');
        $result->assertDontSee('High Risk Report');
        $result->assertDontSee('Low Risk Report');
        
        // Test low risk filter
        $result = $this->get('scam-alerts?risk_level=low');
        $result->assertStatus(200);
        $result->assertSee('Low Risk Report');
        $result->assertDontSee('High Risk Report');
        $result->assertDontSee('Medium Risk Report');
    }

    /**
     * Test: Category filter works correctly
     * 
     * Validates Requirement 11.3: Filtering by category
     */
    public function testCategoryFilterWorksCorrectly(): void
    {
        $db = \Config\Database::connect();
        
        // Create test user
        $userId = $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create categories
        $financeId = $db->table('categories')->insert([
            'name' => 'Finance',
            'slug' => 'finance',
            'description' => 'Financial apps',
            'display_order' => 1,
        ]);
        
        $gamingId = $db->table('categories')->insert([
            'name' => 'Gaming',
            'slug' => 'gaming',
            'description' => 'Gaming apps',
            'display_order' => 2,
        ]);
        
        // Create apps in different categories
        $financeAppId = $db->table('apps')->insert([
            'name' => 'Finance App',
            'slug' => 'finance-app',
            'description' => 'Financial application',
            'platform_type' => 'android',
            'developer_name' => 'Finance Dev',
            'trust_score' => 40.0,
            'approval_status' => 'approved',
        ]);
        
        $gamingAppId = $db->table('apps')->insert([
            'name' => 'Gaming App',
            'slug' => 'gaming-app',
            'description' => 'Gaming application',
            'platform_type' => 'android',
            'developer_name' => 'Gaming Dev',
            'trust_score' => 45.0,
            'approval_status' => 'approved',
        ]);
        
        // Associate apps with categories
        $db->table('app_categories')->insert([
            'app_id' => $financeAppId,
            'category_id' => $financeId,
        ]);
        
        $db->table('app_categories')->insert([
            'app_id' => $gamingAppId,
            'category_id' => $gamingId,
        ]);
        
        // Create scam reports for each app
        $db->table('scam_reports')->insert([
            'app_id' => $financeAppId,
            'user_id' => $userId,
            'title' => 'Finance Scam Report',
            'description' => 'This finance app has been reported for suspicious activity and potential fraud. Users have reported unauthorized transactions.',
            'risk_level' => 'high',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        $db->table('scam_reports')->insert([
            'app_id' => $gamingAppId,
            'user_id' => $userId,
            'title' => 'Gaming Scam Report',
            'description' => 'This gaming app has been reported for in-app purchase scams and misleading advertisements that trick users into spending money.',
            'risk_level' => 'medium',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Test finance category filter
        $result = $this->get("scam-alerts?category={$financeId}");
        $result->assertStatus(200);
        $result->assertSee('Finance Scam Report');
        $result->assertSee('Finance App');
        $result->assertDontSee('Gaming Scam Report');
        $result->assertDontSee('Gaming App');
        
        // Test gaming category filter
        $result = $this->get("scam-alerts?category={$gamingId}");
        $result->assertStatus(200);
        $result->assertSee('Gaming Scam Report');
        $result->assertSee('Gaming App');
        $result->assertDontSee('Finance Scam Report');
        $result->assertDontSee('Finance App');
    }

    /**
     * Test: Risk levels are color-coded correctly
     * 
     * Validates Requirement 11.4: Risk levels displayed with correct colors
     */
    public function testRiskLevelsAreColorCodedCorrectly(): void
    {
        $db = \Config\Database::connect();
        
        // Create test user
        $userId = $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create test app
        $appId = $db->table('apps')->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test application',
            'platform_type' => 'android',
            'developer_name' => 'Test Dev',
            'trust_score' => 50.0,
            'approval_status' => 'approved',
        ]);
        
        // Create scam reports with different risk levels
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'High Risk Report',
            'description' => 'This is a high risk scam report that describes serious security vulnerabilities and data theft issues with this application.',
            'risk_level' => 'high',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        ]);
        
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Medium Risk Report',
            'description' => 'This is a medium risk scam report that describes moderate security concerns and potential privacy issues with this application.',
            'risk_level' => 'medium',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        ]);
        
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Low Risk Report',
            'description' => 'This is a low risk scam report that describes minor concerns and potential issues that users should be aware of with this app.',
            'risk_level' => 'low',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);
        
        // Visit scam alerts page
        $result = $this->get('scam-alerts');
        $body = $result->response()->getBody();
        
        // Assert high risk has red badge (bg-danger)
        $this->assertStringContainsString('bg-danger', $body);
        
        // Assert medium risk has orange/yellow badge (bg-warning)
        $this->assertStringContainsString('bg-warning', $body);
        
        // Assert low risk has green badge (bg-success)
        $this->assertStringContainsString('bg-success', $body);
        
        // Assert risk level text is displayed
        $result->assertSee('High Risk');
        $result->assertSee('Medium Risk');
        $result->assertSee('Low Risk');
    }

    /**
     * Test: Reports sorted by date (descending)
     * 
     * Validates Requirement 11.2: Reports sorted by submission date descending
     */
    public function testReportsSortedByDateDescending(): void
    {
        $db = \Config\Database::connect();
        
        // Create test user
        $userId = $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create test app
        $appId = $db->table('apps')->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test application',
            'platform_type' => 'android',
            'developer_name' => 'Test Dev',
            'trust_score' => 50.0,
            'approval_status' => 'approved',
        ]);
        
        // Create scam reports with different dates
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Oldest Report',
            'description' => 'This is the oldest scam report that was submitted first and should appear last in the list of scam alerts on the page.',
            'risk_level' => 'low',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
        ]);
        
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Middle Report',
            'description' => 'This is a middle scam report that was submitted after the oldest one but before the newest one and should appear in the middle.',
            'risk_level' => 'medium',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        ]);
        
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Newest Report',
            'description' => 'This is the newest scam report that was submitted most recently and should appear first in the list of scam alerts on the page.',
            'risk_level' => 'high',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);
        
        // Visit scam alerts page
        $result = $this->get('scam-alerts');
        $body = $result->response()->getBody();
        
        // Find positions of report titles in the HTML
        $newestPos = strpos($body, 'Newest Report');
        $middlePos = strpos($body, 'Middle Report');
        $oldestPos = strpos($body, 'Oldest Report');
        
        // Assert reports appear in descending order by date
        $this->assertLessThan($middlePos, $newestPos, 'Newest report should appear before middle report');
        $this->assertLessThan($oldestPos, $middlePos, 'Middle report should appear before oldest report');
    }

    /**
     * Test: Links to app detail pages work
     * 
     * Validates Requirement 11.7: Links to app detail pages
     */
    public function testLinksToAppDetailPagesWork(): void
    {
        $db = \Config\Database::connect();
        
        // Create test user
        $userId = $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create test app
        $appId = $db->table('apps')->insert([
            'name' => 'Scam App',
            'slug' => 'scam-app',
            'description' => 'A suspicious application',
            'platform_type' => 'android',
            'developer_name' => 'Scammer Inc',
            'trust_score' => 25.0,
            'approval_status' => 'approved',
        ]);
        
        // Create scam report
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Steals Personal Data',
            'description' => 'This app requests excessive permissions and appears to steal personal data without user consent. Multiple users have reported unauthorized charges.',
            'risk_level' => 'high',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Visit scam alerts page
        $result = $this->get('scam-alerts');
        $body = $result->response()->getBody();
        
        // Assert link to app detail page exists
        $this->assertStringContainsString('apps/scam-app', $body);
        $result->assertSee('View App Details');
    }

    /**
     * Test: Pagination works correctly (20 per page)
     * 
     * Validates Requirement 11.6: Pagination with 20 reports per page
     */
    public function testPaginationWorksCorrectly(): void
    {
        $db = \Config\Database::connect();
        
        // Create test user
        $userId = $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create test app
        $appId = $db->table('apps')->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test application',
            'platform_type' => 'android',
            'developer_name' => 'Test Dev',
            'trust_score' => 50.0,
            'approval_status' => 'approved',
        ]);
        
        // Create 25 scam reports (more than one page)
        for ($i = 1; $i <= 25; $i++) {
            $db->table('scam_reports')->insert([
                'app_id' => $appId,
                'user_id' => $userId,
                'title' => "Scam Report {$i}",
                'description' => "This is scam report number {$i} that describes various security concerns and potential issues with this application that users should be aware of.",
                'risk_level' => ($i % 3 === 0) ? 'high' : (($i % 2 === 0) ? 'medium' : 'low'),
                'evidence_urls' => json_encode([]),
                'approval_status' => 'approved',
                'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} days")),
            ]);
        }
        
        // Visit first page
        $result = $this->get('scam-alerts');
        $result->assertStatus(200);
        
        // Count scam report cards on first page (should be 20)
        $body = $result->response()->getBody();
        $reportCardCount = substr_count($body, 'class="scam-report-card');
        $this->assertEquals(20, $reportCardCount, 'First page should show exactly 20 reports');
        
        // Assert pagination controls are present
        $result->assertSee('Next');
        
        // Assert total count is displayed
        $result->assertSee('25 scam reports found');
        
        // Visit second page
        $result2 = $this->get('scam-alerts?page=2');
        $result2->assertStatus(200);
        
        // Count scam report cards on second page (should be 5)
        $body2 = $result2->response()->getBody();
        $reportCardCount2 = substr_count($body2, 'class="scam-report-card');
        $this->assertEquals(5, $reportCardCount2, 'Second page should show remaining 5 reports');
        
        // Assert pagination controls show correct page
        $result2->assertSee('Previous');
    }

    /**
     * Test: Empty state shows appropriate message
     * 
     * Validates display when no scam reports exist
     */
    public function testEmptyStateShowsMessage(): void
    {
        // Visit scam alerts page with no reports
        $result = $this->get('scam-alerts');
        
        // Assert successful response
        $result->assertStatus(200);
        
        // Assert empty state message is shown
        $result->assertSee('No Scam Reports Found');
        $result->assertSee('0 scam reports found');
    }

    /**
     * Test: Combined filters work correctly
     * 
     * Validates that category and risk level filters can be combined
     */
    public function testCombinedFiltersWorkCorrectly(): void
    {
        $db = \Config\Database::connect();
        
        // Create test user
        $userId = $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        // Create categories
        $financeId = $db->table('categories')->insert([
            'name' => 'Finance',
            'slug' => 'finance',
            'description' => 'Financial apps',
            'display_order' => 1,
        ]);
        
        // Create apps
        $financeAppId = $db->table('apps')->insert([
            'name' => 'Finance App',
            'slug' => 'finance-app',
            'description' => 'Financial application',
            'platform_type' => 'android',
            'developer_name' => 'Finance Dev',
            'trust_score' => 40.0,
            'approval_status' => 'approved',
        ]);
        
        // Associate app with category
        $db->table('app_categories')->insert([
            'app_id' => $financeAppId,
            'category_id' => $financeId,
        ]);
        
        // Create scam reports with different risk levels
        $db->table('scam_reports')->insert([
            'app_id' => $financeAppId,
            'user_id' => $userId,
            'title' => 'Finance High Risk',
            'description' => 'This finance app has been reported for high risk security vulnerabilities and potential fraud that users should be aware of immediately.',
            'risk_level' => 'high',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        $db->table('scam_reports')->insert([
            'app_id' => $financeAppId,
            'user_id' => $userId,
            'title' => 'Finance Medium Risk',
            'description' => 'This finance app has been reported for medium risk security concerns and potential privacy issues that users should be aware of.',
            'risk_level' => 'medium',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Test combined filter: Finance category + High risk
        $result = $this->get("scam-alerts?category={$financeId}&risk_level=high");
        $result->assertStatus(200);
        $result->assertSee('Finance High Risk');
        $result->assertDontSee('Finance Medium Risk');
        
        // Assert filter selections are preserved
        $body = $result->response()->getBody();
        $this->assertStringContainsString('selected', $body);
    }

    /**
     * Test: Clear filters button works
     * 
     * Validates that users can clear all filters
     */
    public function testClearFiltersButtonWorks(): void
    {
        $db = \Config\Database::connect();
        
        // Create test data
        $userId = $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
        ]);
        
        $appId = $db->table('apps')->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test application',
            'platform_type' => 'android',
            'developer_name' => 'Test Dev',
            'trust_score' => 50.0,
            'approval_status' => 'approved',
        ]);
        
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Test Report',
            'description' => 'This is a test scam report that describes various security concerns and potential issues with this application that users should be aware of.',
            'risk_level' => 'high',
            'evidence_urls' => json_encode([]),
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Visit page with filters
        $result = $this->get('scam-alerts?risk_level=high');
        
        // Assert clear filters button is shown
        $result->assertSee('Clear Filters');
        
        // Assert link to clear filters exists
        $body = $result->response()->getBody();
        $this->assertStringContainsString('href="' . base_url('scam-alerts') . '"', $body);
    }
}
