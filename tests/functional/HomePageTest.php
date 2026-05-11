<?php

namespace Tests\Functional;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * HomePageTest
 * 
 * Functional tests for the public home page.
 * Tests trending apps display, category navigation, search form, and statistics.
 */
class HomePageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;
    protected $seed        = TestDataSeeder::class;

    /**
     * Test home page loads successfully
     */
    public function testHomePageLoadsSuccessfully(): void
    {
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('AppTrust');
        $result->assertSee('Discover Trustworthy Apps');
    }

    /**
     * Test home page displays trending apps section
     */
    public function testHomePageDisplaysTrendingApps(): void
    {
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('Trending Apps');
    }

    /**
     * Test home page displays up to 12 trending apps
     */
    public function testHomePageDisplaysMaximum12TrendingApps(): void
    {
        // Create 15 approved apps with trending scores
        $db = \Config\Database::connect();
        
        for ($i = 1; $i <= 15; $i++) {
            $db->table('apps')->insert([
                'name' => "Trending App $i",
                'slug' => "trending-app-$i",
                'description' => "Test app $i",
                'platform_type' => 'android',
                'developer_name' => 'Test Developer',
                'approval_status' => 'approved',
                'trust_score' => 80,
                'trending_score' => 100 - $i, // Descending scores
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        $result = $this->get('/');
        
        $result->assertStatus(200);
        
        // Should see first 12 apps
        $result->assertSee('Trending App 1');
        $result->assertSee('Trending App 12');
        
        // Should NOT see 13th, 14th, 15th apps
        $result->assertDontSee('Trending App 13');
        $result->assertDontSee('Trending App 14');
        $result->assertDontSee('Trending App 15');
    }

    /**
     * Test trending apps display trust score with correct color
     */
    public function testTrendingAppsDisplayTrustScoreWithColor(): void
    {
        $db = \Config\Database::connect();
        
        // Create apps with different trust scores
        $db->table('apps')->insert([
            'name' => 'High Trust App',
            'slug' => 'high-trust-app',
            'description' => 'High trust score app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
            'trust_score' => 85,
            'trending_score' => 100,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $db->table('apps')->insert([
            'name' => 'Medium Trust App',
            'slug' => 'medium-trust-app',
            'description' => 'Medium trust score app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
            'trust_score' => 65,
            'trending_score' => 90,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $db->table('apps')->insert([
            'name' => 'Low Trust App',
            'slug' => 'low-trust-app',
            'description' => 'Low trust score app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
            'trust_score' => 35,
            'trending_score' => 80,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('trust-high'); // Green badge for 85
        $result->assertSee('trust-medium'); // Yellow badge for 65
        $result->assertSee('trust-low'); // Red badge for 35
    }

    /**
     * Test trending apps display name, category, and thumbnail
     */
    public function testTrendingAppsDisplayRequiredInformation(): void
    {
        $db = \Config\Database::connect();
        
        // Create category
        $db->table('categories')->insert([
            'name' => 'Finance',
            'slug' => 'finance',
            'display_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $categoryId = $db->insertID();
        
        // Create app
        $db->table('apps')->insert([
            'name' => 'Test Finance App',
            'slug' => 'test-finance-app',
            'description' => 'A test finance application',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
            'trust_score' => 80,
            'trending_score' => 100,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $appId = $db->insertID();
        
        // Link app to category
        $db->table('app_categories')->insert([
            'app_id' => $appId,
            'category_id' => $categoryId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('Test Finance App');
        $result->assertSee('Finance');
        $result->assertSee('80'); // Trust score
    }

    /**
     * Test category navigation menu displays all categories
     */
    public function testCategoryNavigationMenuDisplaysAllCategories(): void
    {
        $db = \Config\Database::connect();
        
        // Create multiple categories
        $categories = ['Finance', 'AI Tools', 'Gaming', 'Education'];
        
        foreach ($categories as $index => $category) {
            $db->table('categories')->insert([
                'name' => $category,
                'slug' => strtolower(str_replace(' ', '-', $category)),
                'display_order' => $index + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('Browse by Category');
        
        foreach ($categories as $category) {
            $result->assertSee($category);
        }
    }

    /**
     * Test search form is present in header
     */
    public function testSearchFormPresentInHeader(): void
    {
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('Search for apps');
        $result->assertSee('<form', false);
        $result->assertSee('action="' . base_url('search') . '"', false);
    }

    /**
     * Test platform statistics are displayed
     */
    public function testPlatformStatisticsDisplayed(): void
    {
        $db = \Config\Database::connect();
        
        // Create test data
        // Apps
        $db->table('apps')->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test',
            'platform_type' => 'android',
            'developer_name' => 'Test',
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $appId = $db->insertID();
        
        // Users
        $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password', PASSWORD_BCRYPT),
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $userId = $db->insertID();
        
        // Reviews
        $db->table('reviews')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'rating' => 5,
            'title' => 'Great app',
            'review_text' => 'This is a great app with excellent features and performance.',
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Scam reports
        $db->table('scam_reports')->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => 'Suspicious behavior',
            'description' => 'This app exhibits suspicious behavior that may indicate malicious intent or fraudulent activity.',
            'risk_level' => 'medium',
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('Verified Apps');
        $result->assertSee('User Reviews');
        $result->assertSee('Scam Reports');
        $result->assertSee('Active Users');
        
        // Check that numbers are displayed
        $result->assertSee('1'); // At least 1 for each stat
    }

    /**
     * Test page loads in acceptable time (< 1 second)
     * Note: This is a basic test. Real performance testing should use profiling tools.
     */
    public function testPageLoadsInAcceptableTime(): void
    {
        $startTime = microtime(true);
        
        $result = $this->get('/');
        
        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;
        
        $result->assertStatus(200);
        
        // Assert page loads in less than 1 second
        $this->assertLessThan(1.0, $loadTime, 'Home page should load in less than 1 second');
    }

    /**
     * Test newsletter subscription form is present
     */
    public function testNewsletterSubscriptionFormPresent(): void
    {
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('Stay Protected');
        $result->assertSee('Subscribe');
        $result->assertSee('newsletter/subscribe');
    }

    /**
     * Test navigation links are present
     */
    public function testNavigationLinksPresent(): void
    {
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('Home');
        $result->assertSee('Apps');
        $result->assertSee('Categories');
        $result->assertSee('Scam Alerts');
        $result->assertSee('Blog');
    }

    /**
     * Test footer is present with links
     */
    public function testFooterPresent(): void
    {
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('Platform');
        $result->assertSee('Resources');
        $result->assertSee('Legal');
        $result->assertSee('Connect');
    }

    /**
     * Test empty state when no trending apps exist
     */
    public function testEmptyStateWhenNoTrendingApps(): void
    {
        // Don't seed any apps
        $result = $this->get('/');
        
        $result->assertStatus(200);
        $result->assertSee('No trending apps available');
    }
}
