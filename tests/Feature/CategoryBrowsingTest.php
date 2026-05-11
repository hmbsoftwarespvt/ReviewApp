<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * CategoryBrowsingTest
 * 
 * Functional tests for Task 24: Category Browsing
 * 
 * Tests:
 * - Category list displays all categories with icons
 * - Category detail shows all apps in category
 * - Apps sorted by trust score (descending)
 * - Pagination works correctly (24 per page)
 * - Category pages load successfully
 */
class CategoryBrowsingTest extends CIUnitTestCase
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
     * Test: Category list shows all categories with icons
     * 
     * Validates Requirement 13.2: Category navigation menu displays all categories
     */
    public function testCategoryListShowsAllCategoriesWithIcons(): void
    {
        // Create test categories
        $db = \Config\Database::connect();
        
        $categories = [
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Financial apps',
                'icon' => 'cash-coin',
                'display_order' => 1,
            ],
            [
                'name' => 'AI Tools',
                'slug' => 'ai-tools',
                'description' => 'AI-powered applications',
                'icon' => 'robot',
                'display_order' => 2,
            ],
            [
                'name' => 'Gaming',
                'slug' => 'gaming',
                'description' => 'Gaming applications',
                'icon' => 'controller',
                'display_order' => 3,
            ],
        ];
        
        foreach ($categories as $category) {
            $db->table('categories')->insert($category);
        }
        
        // Visit category list page
        $result = $this->get('categories');
        
        // Assert successful response
        $result->assertStatus(200);
        $result->assertSee('Browse Categories');
        
        // Assert all categories are displayed
        $result->assertSee('Finance');
        $result->assertSee('AI Tools');
        $result->assertSee('Gaming');
        
        // Assert descriptions are displayed
        $result->assertSee('Financial apps');
        $result->assertSee('AI-powered applications');
        $result->assertSee('Gaming applications');
        
        // Assert icons are present (check for Bootstrap icon classes)
        $result->assertSee('bi-cash-coin');
        $result->assertSee('bi-robot');
        $result->assertSee('bi-controller');
    }

    /**
     * Test: Category detail shows all apps in category
     * 
     * Validates Requirement 13.3: Category pages display all apps in category
     */
    public function testCategoryDetailShowsAllAppsInCategory(): void
    {
        $db = \Config\Database::connect();
        
        // Create category
        $categoryId = $db->table('categories')->insert([
            'name' => 'Finance',
            'slug' => 'finance',
            'description' => 'Financial apps',
            'icon' => 'cash-coin',
            'display_order' => 1,
        ]);
        
        // Create apps
        $app1Id = $db->table('apps')->insert([
            'name' => 'Banking App',
            'slug' => 'banking-app',
            'description' => 'Secure banking application',
            'platform_type' => 'android',
            'developer_name' => 'Bank Corp',
            'trust_score' => 85.5,
            'approval_status' => 'approved',
        ]);
        
        $app2Id = $db->table('apps')->insert([
            'name' => 'Budget Tracker',
            'slug' => 'budget-tracker',
            'description' => 'Track your expenses',
            'platform_type' => 'ios',
            'developer_name' => 'Finance Inc',
            'trust_score' => 78.2,
            'approval_status' => 'approved',
        ]);
        
        // Associate apps with category
        $db->table('app_categories')->insert([
            'app_id' => $app1Id,
            'category_id' => $categoryId,
        ]);
        
        $db->table('app_categories')->insert([
            'app_id' => $app2Id,
            'category_id' => $categoryId,
        ]);
        
        // Visit category detail page
        $result = $this->get('categories/finance');
        
        // Assert successful response
        $result->assertStatus(200);
        $result->assertSee('Finance');
        
        // Assert both apps are displayed
        $result->assertSee('Banking App');
        $result->assertSee('Budget Tracker');
        
        // Assert app details are shown
        $result->assertSee('Bank Corp');
        $result->assertSee('Finance Inc');
        $result->assertSee('Secure banking application');
        $result->assertSee('Track your expenses');
    }

    /**
     * Test: Apps sorted by trust score (descending)
     * 
     * Validates Requirement 13.5: Apps sorted by trust score descending
     */
    public function testAppsSortedByTrustScoreDescending(): void
    {
        $db = \Config\Database::connect();
        
        // Create category
        $categoryId = $db->table('categories')->insert([
            'name' => 'Finance',
            'slug' => 'finance',
            'description' => 'Financial apps',
            'display_order' => 1,
        ]);
        
        // Create apps with different trust scores
        $apps = [
            [
                'name' => 'Low Score App',
                'slug' => 'low-score-app',
                'description' => 'Low trust score',
                'platform_type' => 'android',
                'developer_name' => 'Dev A',
                'trust_score' => 45.0,
                'approval_status' => 'approved',
            ],
            [
                'name' => 'High Score App',
                'slug' => 'high-score-app',
                'description' => 'High trust score',
                'platform_type' => 'android',
                'developer_name' => 'Dev B',
                'trust_score' => 92.5,
                'approval_status' => 'approved',
            ],
            [
                'name' => 'Medium Score App',
                'slug' => 'medium-score-app',
                'description' => 'Medium trust score',
                'platform_type' => 'android',
                'developer_name' => 'Dev C',
                'trust_score' => 68.3,
                'approval_status' => 'approved',
            ],
        ];
        
        foreach ($apps as $app) {
            $appId = $db->table('apps')->insert($app);
            $db->table('app_categories')->insert([
                'app_id' => $appId,
                'category_id' => $categoryId,
            ]);
        }
        
        // Visit category detail page
        $result = $this->get('categories/finance');
        
        // Get response body
        $body = $result->response()->getBody();
        
        // Find positions of app names in the HTML
        $highPos = strpos($body, 'High Score App');
        $mediumPos = strpos($body, 'Medium Score App');
        $lowPos = strpos($body, 'Low Score App');
        
        // Assert apps appear in descending order by trust score
        $this->assertLessThan($mediumPos, $highPos, 'High score app should appear before medium score app');
        $this->assertLessThan($lowPos, $mediumPos, 'Medium score app should appear before low score app');
        
        // Also verify trust scores are displayed
        $result->assertSee('93'); // High score (rounded)
        $result->assertSee('68'); // Medium score (rounded)
        $result->assertSee('45'); // Low score (rounded)
    }

    /**
     * Test: Pagination works correctly (24 per page)
     * 
     * Validates Requirement 13.6: Pagination with 24 apps per page
     */
    public function testPaginationWorksCorrectly(): void
    {
        $db = \Config\Database::connect();
        
        // Create category
        $categoryId = $db->table('categories')->insert([
            'name' => 'Gaming',
            'slug' => 'gaming',
            'description' => 'Gaming apps',
            'display_order' => 1,
        ]);
        
        // Create 30 apps (more than one page)
        for ($i = 1; $i <= 30; $i++) {
            $appId = $db->table('apps')->insert([
                'name' => "Game App {$i}",
                'slug' => "game-app-{$i}",
                'description' => "Gaming application {$i}",
                'platform_type' => 'android',
                'developer_name' => "Developer {$i}",
                'trust_score' => 50 + $i,
                'approval_status' => 'approved',
            ]);
            
            $db->table('app_categories')->insert([
                'app_id' => $appId,
                'category_id' => $categoryId,
            ]);
        }
        
        // Visit first page
        $result = $this->get('categories/gaming');
        $result->assertStatus(200);
        
        // Count app cards on first page (should be 24)
        $body = $result->response()->getBody();
        $appCardCount = substr_count($body, 'class="card app-card"');
        $this->assertEquals(24, $appCardCount, 'First page should show exactly 24 apps');
        
        // Assert pagination controls are present
        $result->assertSee('Next');
        
        // Visit second page
        $result2 = $this->get('categories/gaming?page=2');
        $result2->assertStatus(200);
        
        // Count app cards on second page (should be 6)
        $body2 = $result2->response()->getBody();
        $appCardCount2 = substr_count($body2, 'class="card app-card"');
        $this->assertEquals(6, $appCardCount2, 'Second page should show remaining 6 apps');
        
        // Assert pagination controls show correct page
        $result2->assertSee('Previous');
    }

    /**
     * Test: Category page not found returns 404
     * 
     * Validates error handling for non-existent categories
     */
    public function testCategoryNotFoundReturns404(): void
    {
        // Try to visit non-existent category
        $result = $this->get('categories/non-existent-category');
        
        // Assert 404 response
        $result->assertStatus(404);
    }

    /**
     * Test: Empty category shows appropriate message
     * 
     * Validates display when category has no apps
     */
    public function testEmptyCategoryShowsMessage(): void
    {
        $db = \Config\Database::connect();
        
        // Create category with no apps
        $db->table('categories')->insert([
            'name' => 'Empty Category',
            'slug' => 'empty-category',
            'description' => 'This category has no apps',
            'display_order' => 1,
        ]);
        
        // Visit category detail page
        $result = $this->get('categories/empty-category');
        
        // Assert successful response
        $result->assertStatus(200);
        
        // Assert empty state message is shown
        $result->assertSee('No apps in this category yet');
        $result->assertSee('Browse Other Categories');
    }

    /**
     * Test: Category list shows app counts
     * 
     * Validates that category list displays correct app counts
     */
    public function testCategoryListShowsAppCounts(): void
    {
        $db = \Config\Database::connect();
        
        // Create categories
        $category1Id = $db->table('categories')->insert([
            'name' => 'Finance',
            'slug' => 'finance',
            'description' => 'Financial apps',
            'display_order' => 1,
        ]);
        
        $category2Id = $db->table('categories')->insert([
            'name' => 'Gaming',
            'slug' => 'gaming',
            'description' => 'Gaming apps',
            'display_order' => 2,
        ]);
        
        // Create apps for Finance category (3 apps)
        for ($i = 1; $i <= 3; $i++) {
            $appId = $db->table('apps')->insert([
                'name' => "Finance App {$i}",
                'slug' => "finance-app-{$i}",
                'description' => "Financial application {$i}",
                'platform_type' => 'android',
                'developer_name' => "Developer {$i}",
                'trust_score' => 70,
                'approval_status' => 'approved',
            ]);
            
            $db->table('app_categories')->insert([
                'app_id' => $appId,
                'category_id' => $category1Id,
            ]);
        }
        
        // Create apps for Gaming category (5 apps)
        for ($i = 1; $i <= 5; $i++) {
            $appId = $db->table('apps')->insert([
                'name' => "Game App {$i}",
                'slug' => "game-app-{$i}",
                'description' => "Gaming application {$i}",
                'platform_type' => 'android',
                'developer_name' => "Developer {$i}",
                'trust_score' => 70,
                'approval_status' => 'approved',
            ]);
            
            $db->table('app_categories')->insert([
                'app_id' => $appId,
                'category_id' => $category2Id,
            ]);
        }
        
        // Visit category list page
        $result = $this->get('categories');
        
        // Assert app counts are displayed
        $result->assertSee('3 Apps'); // Finance category
        $result->assertSee('5 Apps'); // Gaming category
    }

    /**
     * Test: Only approved apps are shown in category
     * 
     * Validates that pending/rejected apps are not displayed
     */
    public function testOnlyApprovedAppsShownInCategory(): void
    {
        $db = \Config\Database::connect();
        
        // Create category
        $categoryId = $db->table('categories')->insert([
            'name' => 'Finance',
            'slug' => 'finance',
            'description' => 'Financial apps',
            'display_order' => 1,
        ]);
        
        // Create approved app
        $approvedAppId = $db->table('apps')->insert([
            'name' => 'Approved App',
            'slug' => 'approved-app',
            'description' => 'This app is approved',
            'platform_type' => 'android',
            'developer_name' => 'Dev A',
            'trust_score' => 80,
            'approval_status' => 'approved',
        ]);
        
        // Create pending app
        $pendingAppId = $db->table('apps')->insert([
            'name' => 'Pending App',
            'slug' => 'pending-app',
            'description' => 'This app is pending',
            'platform_type' => 'android',
            'developer_name' => 'Dev B',
            'trust_score' => 75,
            'approval_status' => 'pending',
        ]);
        
        // Create rejected app
        $rejectedAppId = $db->table('apps')->insert([
            'name' => 'Rejected App',
            'slug' => 'rejected-app',
            'description' => 'This app is rejected',
            'platform_type' => 'android',
            'developer_name' => 'Dev C',
            'trust_score' => 70,
            'approval_status' => 'rejected',
        ]);
        
        // Associate all apps with category
        foreach ([$approvedAppId, $pendingAppId, $rejectedAppId] as $appId) {
            $db->table('app_categories')->insert([
                'app_id' => $appId,
                'category_id' => $categoryId,
            ]);
        }
        
        // Visit category detail page
        $result = $this->get('categories/finance');
        
        // Assert only approved app is shown
        $result->assertSee('Approved App');
        $result->assertDontSee('Pending App');
        $result->assertDontSee('Rejected App');
    }

    /**
     * Test: Category detail page shows correct total count
     * 
     * Validates that the total app count is displayed correctly
     */
    public function testCategoryDetailShowsCorrectTotalCount(): void
    {
        $db = \Config\Database::connect();
        
        // Create category
        $categoryId = $db->table('categories')->insert([
            'name' => 'Finance',
            'slug' => 'finance',
            'description' => 'Financial apps',
            'display_order' => 1,
        ]);
        
        // Create 5 apps
        for ($i = 1; $i <= 5; $i++) {
            $appId = $db->table('apps')->insert([
                'name' => "Finance App {$i}",
                'slug' => "finance-app-{$i}",
                'description' => "Financial application {$i}",
                'platform_type' => 'android',
                'developer_name' => "Developer {$i}",
                'trust_score' => 70,
                'approval_status' => 'approved',
            ]);
            
            $db->table('app_categories')->insert([
                'app_id' => $appId,
                'category_id' => $categoryId,
            ]);
        }
        
        // Visit category detail page
        $result = $this->get('categories/finance');
        
        // Assert total count is displayed
        $result->assertSee('5 Apps');
        $result->assertSee('Showing 5 of 5 apps');
    }
}

