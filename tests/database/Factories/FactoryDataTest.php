<?php

namespace Tests\Database\Factories;

use CodeIgniter\Test\CIUnitTestCase;
use App\Database\Factories\UserFactory;
use App\Database\Factories\AppFactory;
use App\Database\Factories\ReviewFactory;
use App\Database\Factories\ScamReportFactory;
use App\Database\Factories\CategoryFactory;
use App\Database\Factories\BlogPostFactory;
use App\Database\Factories\ScreenshotFactory;
use App\Database\Factories\NewsletterSubscriberFactory;
use App\Database\Factories\SettingFactory;
use App\Database\Factories\ActivityLogFactory;
use App\Database\Factories\ReviewHelpfulVoteFactory;

/**
 * Factory Data Test
 * 
 * Tests that all factories generate valid data structures without database validation.
 */
class FactoryDataTest extends CIUnitTestCase
{
    public function testUserFactoryGeneratesRequiredFields()
    {
        $factory = new UserFactory();
        $userData = $factory->make();
        
        $this->assertArrayHasKey('username', $userData);
        $this->assertArrayHasKey('email', $userData);
        $this->assertArrayHasKey('password_hash', $userData);
        $this->assertArrayHasKey('role', $userData);
        $this->assertArrayHasKey('status', $userData);
        
        // Check password hash length (bcrypt is 60 chars)
        $this->assertGreaterThanOrEqual(60, strlen($userData['password_hash']));
        
        // Check role is valid
        $this->assertContains($userData['role'], ['user', 'admin']);
        
        // Check status is valid
        $this->assertContains($userData['status'], ['active', 'suspended', 'deleted']);
    }

    public function testAppFactoryGeneratesRequiredFields()
    {
        $factory = new AppFactory();
        $appData = $factory->make();
        
        $this->assertArrayHasKey('name', $appData);
        $this->assertArrayHasKey('slug', $appData);
        $this->assertArrayHasKey('platform_type', $appData);
        $this->assertArrayHasKey('developer_name', $appData);
        $this->assertArrayHasKey('trust_score', $appData);
        $this->assertArrayHasKey('security_score', $appData);
        $this->assertArrayHasKey('developer_reputation', $appData);
        
        // Check trust score range
        $this->assertGreaterThanOrEqual(0, $appData['trust_score']);
        $this->assertLessThanOrEqual(100, $appData['trust_score']);
        
        // Check security score range
        $this->assertGreaterThanOrEqual(0, $appData['security_score']);
        $this->assertLessThanOrEqual(25, $appData['security_score']);
        
        // Check developer reputation range
        $this->assertGreaterThanOrEqual(0, $appData['developer_reputation']);
        $this->assertLessThanOrEqual(20, $appData['developer_reputation']);
        
        // Check platform type is valid
        $this->assertContains($appData['platform_type'], ['android', 'ios', 'web', 'desktop']);
        
        // Check permissions is valid JSON
        $permissions = json_decode($appData['permissions'], true);
        $this->assertIsArray($permissions);
    }

    public function testReviewFactoryGeneratesRequiredFields()
    {
        $factory = new ReviewFactory();
        $reviewData = $factory->make(['app_id' => 1, 'user_id' => 1]);
        
        $this->assertArrayHasKey('rating', $reviewData);
        $this->assertArrayHasKey('title', $reviewData);
        $this->assertArrayHasKey('review_text', $reviewData);
        $this->assertArrayHasKey('approval_status', $reviewData);
        
        // Check rating range
        $this->assertGreaterThanOrEqual(1, $reviewData['rating']);
        $this->assertLessThanOrEqual(5, $reviewData['rating']);
        
        // Check review text length
        $this->assertGreaterThanOrEqual(50, strlen($reviewData['review_text']));
        $this->assertLessThanOrEqual(2000, strlen($reviewData['review_text']));
        
        // Check approval status is valid
        $this->assertContains($reviewData['approval_status'], ['pending', 'approved', 'rejected']);
    }

    public function testScamReportFactoryGeneratesRequiredFields()
    {
        $factory = new ScamReportFactory();
        $reportData = $factory->make(['app_id' => 1, 'user_id' => 1]);
        
        $this->assertArrayHasKey('title', $reportData);
        $this->assertArrayHasKey('description', $reportData);
        $this->assertArrayHasKey('risk_level', $reportData);
        $this->assertArrayHasKey('evidence_urls', $reportData);
        
        // Check description length
        $this->assertGreaterThanOrEqual(100, strlen($reportData['description']));
        $this->assertLessThanOrEqual(3000, strlen($reportData['description']));
        
        // Check risk level is valid
        $this->assertContains($reportData['risk_level'], ['low', 'medium', 'high']);
        
        // Check evidence URLs
        $evidenceUrls = json_decode($reportData['evidence_urls'], true);
        $this->assertIsArray($evidenceUrls);
        $this->assertLessThanOrEqual(5, count($evidenceUrls));
    }

    public function testCategoryFactoryGeneratesRequiredFields()
    {
        $factory = new CategoryFactory();
        $categoryData = $factory->make();
        
        $this->assertArrayHasKey('name', $categoryData);
        $this->assertArrayHasKey('slug', $categoryData);
        $this->assertArrayHasKey('icon', $categoryData);
        
        // Check slug format (alphanumeric with dashes)
        $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $categoryData['slug']);
    }

    public function testBlogPostFactoryGeneratesRequiredFields()
    {
        $factory = new BlogPostFactory();
        $postData = $factory->make(['author_id' => 1]);
        
        $this->assertArrayHasKey('title', $postData);
        $this->assertArrayHasKey('slug', $postData);
        $this->assertArrayHasKey('content', $postData);
        $this->assertArrayHasKey('category', $postData);
        $this->assertArrayHasKey('publication_status', $postData);
        
        // Check category is valid
        $this->assertContains($postData['category'], ['guides', 'tips_tricks', 'scam_alerts', 'news_updates', 'reviews']);
        
        // Check publication status is valid
        $this->assertContains($postData['publication_status'], ['draft', 'published']);
    }

    public function testScreenshotFactoryGeneratesRequiredFields()
    {
        $factory = new ScreenshotFactory();
        $screenshotData = $factory->make(['app_id' => 1]);
        
        $this->assertArrayHasKey('filename', $screenshotData);
        $this->assertArrayHasKey('file_path', $screenshotData);
        $this->assertArrayHasKey('display_order', $screenshotData);
    }

    public function testNewsletterSubscriberFactoryGeneratesRequiredFields()
    {
        $factory = new NewsletterSubscriberFactory();
        $subscriberData = $factory->make();
        
        $this->assertArrayHasKey('email', $subscriberData);
        $this->assertArrayHasKey('unsubscribe_token', $subscriberData);
        $this->assertArrayHasKey('is_confirmed', $subscriberData);
        
        // Check email format
        $this->assertMatchesRegularExpression('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $subscriberData['email']);
        
        // Check token length
        $this->assertGreaterThan(0, strlen($subscriberData['unsubscribe_token']));
    }

    public function testSettingFactoryGeneratesRequiredFields()
    {
        $factory = new SettingFactory();
        $settingData = $factory->make();
        
        $this->assertArrayHasKey('setting_key', $settingData);
        $this->assertArrayHasKey('setting_value', $settingData);
        $this->assertArrayHasKey('setting_type', $settingData);
        
        // Check setting type is valid
        $this->assertContains($settingData['setting_type'], ['string', 'integer', 'float', 'boolean', 'json']);
    }

    public function testActivityLogFactoryGeneratesRequiredFields()
    {
        $factory = new ActivityLogFactory();
        $activityData = $factory->make(['app_id' => 1]);
        
        $this->assertArrayHasKey('activity_type', $activityData);
        $this->assertArrayHasKey('activity_date', $activityData);
        $this->assertArrayHasKey('count', $activityData);
        
        // Check activity type is valid
        $this->assertContains($activityData['activity_type'], ['view', 'review', 'scam_report']);
        
        // Check count is positive
        $this->assertGreaterThanOrEqual(0, $activityData['count']);
    }

    public function testReviewHelpfulVoteFactoryGeneratesRequiredFields()
    {
        $factory = new ReviewHelpfulVoteFactory();
        $voteData = $factory->make(['review_id' => 1, 'user_id' => 1]);
        
        $this->assertArrayHasKey('review_id', $voteData);
        $this->assertArrayHasKey('user_id', $voteData);
    }

    public function testAllFactoriesGenerateUniqueData()
    {
        $userFactory = new UserFactory();
        $user1 = $userFactory->make();
        $user2 = $userFactory->make();
        
        // Usernames and emails should be unique
        $this->assertNotEquals($user1['username'], $user2['username']);
        $this->assertNotEquals($user1['email'], $user2['email']);
        
        $appFactory = new AppFactory();
        $app1 = $appFactory->make();
        $app2 = $appFactory->make();
        
        // Slugs should be unique
        $this->assertNotEquals($app1['slug'], $app2['slug']);
    }

    public function testFactoryMakeManyWorks()
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
        $userData = $factory->make(['email' => 'custom@example.com', 'role' => 'admin']);
        
        $this->assertEquals('custom@example.com', $userData['email']);
        $this->assertEquals('admin', $userData['role']);
    }

    public function testAllFactoryHelperMethodsWork()
    {
        // User factory helpers
        $userFactory = new UserFactory();
        $this->assertEquals('admin', $userFactory->admin()['role']);
        $this->assertEquals('user', $userFactory->user()['role']);
        $this->assertTrue($userFactory->verified()['email_verified']);
        $this->assertEquals('suspended', $userFactory->suspended()['status']);
        
        // App factory helpers
        $appFactory = new AppFactory();
        $this->assertEquals('approved', $appFactory->approved()['approval_status']);
        $this->assertGreaterThanOrEqual(80, $appFactory->highTrust()['trust_score']);
        $this->assertEquals(0.00, $appFactory->free()['price']);
        $this->assertEquals('android', $appFactory->android()['platform_type']);
        
        // Review factory helpers
        $reviewFactory = new ReviewFactory();
        $this->assertEquals(5, $reviewFactory->fiveStars(['app_id' => 1, 'user_id' => 1])['rating']);
        $this->assertEquals(1, $reviewFactory->oneStar(['app_id' => 1, 'user_id' => 1])['rating']);
        
        // Scam report factory helpers
        $scamFactory = new ScamReportFactory();
        $this->assertEquals('high', $scamFactory->highRisk(['app_id' => 1, 'user_id' => 1])['risk_level']);
        $this->assertEquals('medium', $scamFactory->mediumRisk(['app_id' => 1, 'user_id' => 1])['risk_level']);
        $this->assertEquals('low', $scamFactory->lowRisk(['app_id' => 1, 'user_id' => 1])['risk_level']);
    }
}
