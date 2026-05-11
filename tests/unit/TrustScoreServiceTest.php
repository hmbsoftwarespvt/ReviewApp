<?php

namespace Tests\Unit;

use App\Services\TrustScoreService;
use App\Models\AppModel;
use App\Models\ReviewModel;
use App\Models\ScamReportModel;
use App\Models\SettingModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * TrustScoreServiceTest
 * 
 * Unit tests for TrustScoreService
 */
class TrustScoreServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected TrustScoreService $service;
    protected AppModel $appModel;
    protected ReviewModel $reviewModel;
    protected ScamReportModel $scamReportModel;
    protected SettingModel $settingModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->appModel = new AppModel();
        $this->reviewModel = new ReviewModel();
        $this->scamReportModel = new ScamReportModel();
        $this->settingModel = new SettingModel();
        $this->service = new TrustScoreService();
        
        // Set up default trust algorithm weights
        $this->settingModel->setTrustAlgorithmWeights([
            'review_rating_weight' => 30,
            'security_score_weight' => 25,
            'developer_reputation_weight' => 20,
            'scam_report_weight' => 15,
            'app_age_weight' => 10,
        ]);
    }

    /**
     * Test that trust score is always between 0 and 100
     */
    public function testTrustScoreIsWithinRange(): void
    {
        // Create an app with maximum scores
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app-max',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'release_date' => date('Y-m-d', strtotime('-400 days')),
            'security_score' => 25.0,
            'developer_reputation' => 20.0,
            'approval_status' => 'approved',
        ]);
        
        // Create a user for reviews
        $userId = $this->createTestUser();
        
        // Add a 5-star review
        $this->reviewModel->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'rating' => 5,
            'title' => 'Excellent app',
            'review_text' => str_repeat('This is a great app. ', 10),
            'approval_status' => 'approved',
        ]);
        
        $score = $this->service->calculateTrustScore($appId);
        
        $this->assertGreaterThanOrEqual(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }

    /**
     * Test that trust score breakdown components sum to total score
     */
    public function testTrustScoreBreakdownSumsToTotal(): void
    {
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app-breakdown',
            'platform_type' => 'ios',
            'developer_name' => 'Test Developer',
            'release_date' => date('Y-m-d', strtotime('-200 days')),
            'security_score' => 15.0,
            'developer_reputation' => 10.0,
            'approval_status' => 'approved',
        ]);
        
        $breakdown = $this->service->getTrustScoreBreakdown($appId);
        
        $calculatedTotal = $breakdown['review_rating_contribution']
                         + $breakdown['security_score_contribution']
                         + $breakdown['developer_reputation_contribution']
                         + $breakdown['scam_report_contribution']
                         + $breakdown['app_age_contribution'];
        
        $this->assertEquals($breakdown['total_score'], round($calculatedTotal, 2));
    }

    /**
     * Test review rating contribution calculation
     */
    public function testReviewRatingContribution(): void
    {
        $userId = $this->createTestUser();
        
        // Test 4.5-5.0 range (30 points)
        $appId1 = $this->createAppWithReviews($userId, [5, 5, 4]);
        $breakdown1 = $this->service->getTrustScoreBreakdown($appId1);
        $this->assertEquals(30.0, $breakdown1['review_rating_contribution']);
        
        // Test 3.5-4.4 range (22 points)
        $appId2 = $this->createAppWithReviews($userId, [4, 4, 3], 'app-2');
        $breakdown2 = $this->service->getTrustScoreBreakdown($appId2);
        $this->assertEquals(22.0, $breakdown2['review_rating_contribution']);
        
        // Test 2.5-3.4 range (15 points)
        $appId3 = $this->createAppWithReviews($userId, [3, 3, 3], 'app-3');
        $breakdown3 = $this->service->getTrustScoreBreakdown($appId3);
        $this->assertEquals(15.0, $breakdown3['review_rating_contribution']);
        
        // Test 1.5-2.4 range (8 points)
        $appId4 = $this->createAppWithReviews($userId, [2, 2, 2], 'app-4');
        $breakdown4 = $this->service->getTrustScoreBreakdown($appId4);
        $this->assertEquals(8.0, $breakdown4['review_rating_contribution']);
        
        // Test <1.5 range (0 points)
        $appId5 = $this->createAppWithReviews($userId, [1, 1, 1], 'app-5');
        $breakdown5 = $this->service->getTrustScoreBreakdown($appId5);
        $this->assertEquals(0.0, $breakdown5['review_rating_contribution']);
    }

    /**
     * Test scam report contribution calculation
     */
    public function testScamReportContribution(): void
    {
        $userId = $this->createTestUser();
        
        // Test 0 reports (15 points)
        $appId1 = $this->createAppWithScamReports($userId, 0);
        $breakdown1 = $this->service->getTrustScoreBreakdown($appId1);
        $this->assertEquals(15.0, $breakdown1['scam_report_contribution']);
        
        // Test 1-5 reports (10 points)
        $appId2 = $this->createAppWithScamReports($userId, 3, 'app-scam-2');
        $breakdown2 = $this->service->getTrustScoreBreakdown($appId2);
        $this->assertEquals(10.0, $breakdown2['scam_report_contribution']);
        
        // Test 6-15 reports (5 points)
        $appId3 = $this->createAppWithScamReports($userId, 10, 'app-scam-3');
        $breakdown3 = $this->service->getTrustScoreBreakdown($appId3);
        $this->assertEquals(5.0, $breakdown3['scam_report_contribution']);
        
        // Test >15 reports (0 points)
        $appId4 = $this->createAppWithScamReports($userId, 20, 'app-scam-4');
        $breakdown4 = $this->service->getTrustScoreBreakdown($appId4);
        $this->assertEquals(0.0, $breakdown4['scam_report_contribution']);
    }

    /**
     * Test app age contribution calculation
     */
    public function testAppAgeContribution(): void
    {
        // Test >365 days (10 points)
        $appId1 = $this->appModel->insert([
            'name' => 'Old App',
            'slug' => 'old-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'release_date' => date('Y-m-d', strtotime('-400 days')),
            'approval_status' => 'approved',
        ]);
        $breakdown1 = $this->service->getTrustScoreBreakdown($appId1);
        $this->assertEquals(10.0, $breakdown1['app_age_contribution']);
        
        // Test 180-365 days (7 points)
        $appId2 = $this->appModel->insert([
            'name' => 'Medium App',
            'slug' => 'medium-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'release_date' => date('Y-m-d', strtotime('-250 days')),
            'approval_status' => 'approved',
        ]);
        $breakdown2 = $this->service->getTrustScoreBreakdown($appId2);
        $this->assertEquals(7.0, $breakdown2['app_age_contribution']);
        
        // Test 90-179 days (4 points)
        $appId3 = $this->appModel->insert([
            'name' => 'Recent App',
            'slug' => 'recent-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'release_date' => date('Y-m-d', strtotime('-120 days')),
            'approval_status' => 'approved',
        ]);
        $breakdown3 = $this->service->getTrustScoreBreakdown($appId3);
        $this->assertEquals(4.0, $breakdown3['app_age_contribution']);
        
        // Test <90 days (2 points)
        $appId4 = $this->appModel->insert([
            'name' => 'New App',
            'slug' => 'new-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'release_date' => date('Y-m-d', strtotime('-30 days')),
            'approval_status' => 'approved',
        ]);
        $breakdown4 = $this->service->getTrustScoreBreakdown($appId4);
        $this->assertEquals(2.0, $breakdown4['app_age_contribution']);
    }

    /**
     * Test score color classification
     */
    public function testScoreColor(): void
    {
        // Test green (80-100)
        $this->assertEquals('green', $this->service->getScoreColor(100));
        $this->assertEquals('green', $this->service->getScoreColor(85));
        $this->assertEquals('green', $this->service->getScoreColor(80));
        
        // Test yellow (50-79)
        $this->assertEquals('yellow', $this->service->getScoreColor(79));
        $this->assertEquals('yellow', $this->service->getScoreColor(65));
        $this->assertEquals('yellow', $this->service->getScoreColor(50));
        
        // Test red (0-49)
        $this->assertEquals('red', $this->service->getScoreColor(49));
        $this->assertEquals('red', $this->service->getScoreColor(25));
        $this->assertEquals('red', $this->service->getScoreColor(0));
    }

    /**
     * Test security score contribution is capped at 25
     */
    public function testSecurityScoreContributionCapped(): void
    {
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app-security',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'security_score' => 30.0, // Exceeds maximum
            'approval_status' => 'approved',
        ]);
        
        $breakdown = $this->service->getTrustScoreBreakdown($appId);
        
        $this->assertEquals(25.0, $breakdown['security_score_contribution']);
    }

    /**
     * Test developer reputation contribution is capped at 20
     */
    public function testDeveloperReputationContributionCapped(): void
    {
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app-reputation',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'developer_reputation' => 25.0, // Exceeds maximum
            'approval_status' => 'approved',
        ]);
        
        $breakdown = $this->service->getTrustScoreBreakdown($appId);
        
        $this->assertEquals(20.0, $breakdown['developer_reputation_contribution']);
    }

    /**
     * Test that non-existent app returns 0 score
     */
    public function testNonExistentAppReturnsZero(): void
    {
        $score = $this->service->calculateTrustScore(99999);
        
        $this->assertEquals(0.0, $score);
    }

    /**
     * Helper: Create a test user
     */
    protected function createTestUser(): int
    {
        $db = \Config\Database::connect();
        $db->table('users')->insert([
            'username' => 'testuser_' . uniqid(),
            'email' => 'test_' . uniqid() . '@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        return $db->insertID();
    }

    /**
     * Helper: Create an app with reviews
     */
    protected function createAppWithReviews(int $userId, array $ratings, string $slug = 'test-app-reviews'): int
    {
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => $slug,
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);
        
        foreach ($ratings as $index => $rating) {
            // Create additional users for each review (one review per user per app)
            $reviewUserId = $index === 0 ? $userId : $this->createTestUser();
            
            $this->reviewModel->insert([
                'app_id' => $appId,
                'user_id' => $reviewUserId,
                'rating' => $rating,
                'title' => 'Review ' . ($index + 1),
                'review_text' => str_repeat('This is a review. ', 10),
                'approval_status' => 'approved',
            ]);
        }
        
        return $appId;
    }

    /**
     * Helper: Create an app with scam reports
     */
    protected function createAppWithScamReports(int $userId, int $count, string $slug = 'test-app-scam'): int
    {
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => $slug,
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);
        
        for ($i = 0; $i < $count; $i++) {
            // Create additional users for each report
            $reportUserId = $i === 0 ? $userId : $this->createTestUser();
            
            $this->scamReportModel->insert([
                'app_id' => $appId,
                'user_id' => $reportUserId,
                'title' => 'Scam Report ' . ($i + 1),
                'description' => str_repeat('This is a scam report. ', 20),
                'risk_level' => 'medium',
                'approval_status' => 'approved',
            ]);
        }
        
        return $appId;
    }
}
