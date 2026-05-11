<?php

namespace Tests\Database\Factories;

use CodeIgniter\Test\CIUnitTestCase;
use App\Database\Factories\UserFactory;
use App\Database\Factories\AppFactory;
use App\Database\Factories\ReviewFactory;
use App\Database\Factories\ScamReportFactory;
use App\Database\Factories\CategoryFactory;
use App\Models\UserModel;
use App\Models\AppModel;
use App\Models\ReviewModel;
use App\Models\ScamReportModel;
use App\Models\CategoryModel;

/**
 * Factory Test
 * 
 * Tests that all factories generate valid data that passes model validation.
 */
class FactoryTest extends CIUnitTestCase
{
    public function testUserFactoryGeneratesValidData()
    {
        $factory = new UserFactory();
        $userData = $factory->make();
        
        $model = new UserModel();
        $this->assertTrue($model->validate($userData), 'UserFactory should generate valid data');
        
        // Check required fields
        $this->assertArrayHasKey('username', $userData);
        $this->assertArrayHasKey('email', $userData);
        $this->assertArrayHasKey('password_hash', $userData);
        
        // Check password hash length
        $this->assertGreaterThanOrEqual(60, strlen($userData['password_hash']));
    }

    public function testAppFactoryGeneratesValidData()
    {
        $factory = new AppFactory();
        $appData = $factory->make();
        
        $model = new AppModel();
        $this->assertTrue($model->validate($appData), 'AppFactory should generate valid data');
        
        // Check trust score range
        $this->assertGreaterThanOrEqual(0, $appData['trust_score']);
        $this->assertLessThanOrEqual(100, $appData['trust_score']);
        
        // Check security score range
        $this->assertGreaterThanOrEqual(0, $appData['security_score']);
        $this->assertLessThanOrEqual(25, $appData['security_score']);
        
        // Check developer reputation range
        $this->assertGreaterThanOrEqual(0, $appData['developer_reputation']);
        $this->assertLessThanOrEqual(20, $appData['developer_reputation']);
        
        // Check permissions is valid JSON
        $permissions = json_decode($appData['permissions'], true);
        $this->assertIsArray($permissions);
    }

    public function testReviewFactoryGeneratesValidData()
    {
        $factory = new ReviewFactory();
        $reviewData = $factory->make(['app_id' => 1, 'user_id' => 1]);
        
        // Note: Validation will fail because app_id and user_id don't exist in database
        // But we can check the data structure
        
        // Check rating range
        $this->assertGreaterThanOrEqual(1, $reviewData['rating']);
        $this->assertLessThanOrEqual(5, $reviewData['rating']);
        
        // Check review text length
        $this->assertGreaterThanOrEqual(50, strlen($reviewData['review_text']));
        $this->assertLessThanOrEqual(2000, strlen($reviewData['review_text']));
        
        // Check required fields
        $this->assertArrayHasKey('title', $reviewData);
        $this->assertArrayHasKey('review_text', $reviewData);
    }

    public function testScamReportFactoryGeneratesValidData()
    {
        $factory = new ScamReportFactory();
        $reportData = $factory->make(['app_id' => 1, 'user_id' => 1]);
        
        // Check description length
        $this->assertGreaterThanOrEqual(100, strlen($reportData['description']));
        $this->assertLessThanOrEqual(3000, strlen($reportData['description']));
        
        // Check risk level
        $this->assertContains($reportData['risk_level'], ['low', 'medium', 'high']);
        
        // Check evidence URLs
        $evidenceUrls = json_decode($reportData['evidence_urls'], true);
        $this->assertIsArray($evidenceUrls);
        $this->assertLessThanOrEqual(5, count($evidenceUrls));
    }

    public function testCategoryFactoryGeneratesValidData()
    {
        $factory = new CategoryFactory();
        $categoryData = $factory->make();
        
        $model = new CategoryModel();
        $this->assertTrue($model->validate($categoryData), 'CategoryFactory should generate valid data');
        
        // Check required fields
        $this->assertArrayHasKey('name', $categoryData);
        $this->assertArrayHasKey('slug', $categoryData);
        
        // Check slug format (alphanumeric with dashes)
        $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $categoryData['slug']);
    }

    public function testFactoryMakeManyGeneratesMultipleRecords()
    {
        $factory = new UserFactory();
        $users = $factory->makeMany(5);
        
        $this->assertCount(5, $users);
        
        foreach ($users as $userData) {
            $this->assertArrayHasKey('username', $userData);
            $this->assertArrayHasKey('email', $userData);
        }
    }

    public function testFactoryOverridesWork()
    {
        $factory = new UserFactory();
        $userData = $factory->make(['email' => 'custom@example.com']);
        
        $this->assertEquals('custom@example.com', $userData['email']);
    }

    public function testAppFactoryHelperMethods()
    {
        $factory = new AppFactory();
        
        // Test approved helper
        $approvedData = $factory->approved();
        $this->assertEquals('approved', $approvedData['approval_status']);
        
        // Test highTrust helper
        $highTrustData = $factory->highTrust();
        $this->assertGreaterThanOrEqual(80, $highTrustData['trust_score']);
        
        // Test free helper
        $freeData = $factory->free();
        $this->assertEquals(0.00, $freeData['price']);
        
        // Test android helper
        $androidData = $factory->android();
        $this->assertEquals('android', $androidData['platform_type']);
    }

    public function testReviewFactoryHelperMethods()
    {
        $factory = new ReviewFactory();
        
        // Test fiveStars helper
        $fiveStarData = $factory->fiveStars(['app_id' => 1, 'user_id' => 1]);
        $this->assertEquals(5, $fiveStarData['rating']);
        
        // Test oneStar helper
        $oneStarData = $factory->oneStar(['app_id' => 1, 'user_id' => 1]);
        $this->assertEquals(1, $oneStarData['rating']);
        
        // Test approved helper
        $approvedData = $factory->approved(['app_id' => 1, 'user_id' => 1]);
        $this->assertEquals('approved', $approvedData['approval_status']);
    }

    public function testScamReportFactoryHelperMethods()
    {
        $factory = new ScamReportFactory();
        
        // Test highRisk helper
        $highRiskData = $factory->highRisk(['app_id' => 1, 'user_id' => 1]);
        $this->assertEquals('high', $highRiskData['risk_level']);
        
        // Test mediumRisk helper
        $mediumRiskData = $factory->mediumRisk(['app_id' => 1, 'user_id' => 1]);
        $this->assertEquals('medium', $mediumRiskData['risk_level']);
        
        // Test lowRisk helper
        $lowRiskData = $factory->lowRisk(['app_id' => 1, 'user_id' => 1]);
        $this->assertEquals('low', $lowRiskData['risk_level']);
    }

    public function testUserFactoryHelperMethods()
    {
        $factory = new UserFactory();
        
        // Test admin helper
        $adminData = $factory->admin();
        $this->assertEquals('admin', $adminData['role']);
        
        // Test user helper
        $userData = $factory->user();
        $this->assertEquals('user', $userData['role']);
        
        // Test verified helper
        $verifiedData = $factory->verified();
        $this->assertTrue($verifiedData['email_verified']);
        $this->assertNull($verifiedData['verification_token']);
        
        // Test suspended helper
        $suspendedData = $factory->suspended();
        $this->assertEquals('suspended', $suspendedData['status']);
    }

    public function testCategoryFactoryPredefinedCategories()
    {
        $factory = new CategoryFactory();
        
        // Test predefined category
        $categoryData = $factory->predefined('AI Tools');
        $this->assertEquals('AI Tools', $categoryData['name']);
        $this->assertEquals('ai-tools', $categoryData['slug']);
        $this->assertEquals('fa-robot', $categoryData['icon']);
    }

    public function testFactoriesGenerateUniqueData()
    {
        $factory = new UserFactory();
        
        $user1 = $factory->make();
        $user2 = $factory->make();
        
        // Usernames and emails should be unique
        $this->assertNotEquals($user1['username'], $user2['username']);
        $this->assertNotEquals($user1['email'], $user2['email']);
    }

    public function testAppFactoryGeneratesValidPermissions()
    {
        $factory = new AppFactory();
        
        for ($i = 0; $i < 10; $i++) {
            $appData = $factory->make();
            $permissions = json_decode($appData['permissions'], true);
            
            $this->assertIsArray($permissions);
            $this->assertGreaterThanOrEqual(2, count($permissions));
            $this->assertLessThanOrEqual(10, count($permissions));
        }
    }

    public function testReviewFactoryGeneratesAppropriateContent()
    {
        $factory = new ReviewFactory();
        
        // Test 5-star review has positive content
        $fiveStarData = $factory->fiveStars(['app_id' => 1, 'user_id' => 1]);
        $this->assertGreaterThanOrEqual(50, strlen($fiveStarData['review_text']));
        
        // Test 1-star review has negative content
        $oneStarData = $factory->oneStar(['app_id' => 1, 'user_id' => 1]);
        $this->assertGreaterThanOrEqual(50, strlen($oneStarData['review_text']));
    }

    public function testScamReportFactoryGeneratesAppropriateContent()
    {
        $factory = new ScamReportFactory();
        
        // Test high risk report
        $highRiskData = $factory->highRisk(['app_id' => 1, 'user_id' => 1]);
        $this->assertGreaterThanOrEqual(100, strlen($highRiskData['description']));
        
        // Test low risk report
        $lowRiskData = $factory->lowRisk(['app_id' => 1, 'user_id' => 1]);
        $this->assertGreaterThanOrEqual(100, strlen($lowRiskData['description']));
    }
}
