<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * Functional tests for Task 23: Search Functionality
 * 
 * Tests all acceptance criteria:
 * - Search works on app name, developer name, description
 * - Results returned in < 2 seconds
 * - Filters work correctly
 * - Sorting options work
 * - Search terms highlighted in results
 * - Pagination (20 per page)
 */
class SearchFunctionalityTest extends CIUnitTestCase
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
     * Test: Search works on app name
     * 
     * Acceptance Criteria: Search works on app name, developer name, description
     */
    public function testSearchByAppName()
    {
        // Create test app with specific name
        $appModel = model('AppModel');
        $appId = $appModel->insert([
            'name' => 'TestSearchApp',
            'slug' => 'testsearchapp',
            'developer_name' => 'Test Developer',
            'description' => 'A test application',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Search for the app by name
        $result = $this->get('search?q=TestSearchApp');

        $result->assertStatus(200);
        $result->assertSee('TestSearchApp');
        $result->assertSee('Test Developer');
    }

    /**
     * Test: Search works on developer name
     * 
     * Acceptance Criteria: Search works on app name, developer name, description
     */
    public function testSearchByDeveloperName()
    {
        // Create test app with specific developer
        $appModel = model('AppModel');
        $appId = $appModel->insert([
            'name' => 'Developer Test App',
            'slug' => 'developer-test-app',
            'developer_name' => 'UniqueDevName',
            'description' => 'A test application',
            'platform_type' => 'ios',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 80.0,
        ]);

        // Search for the app by developer name
        $result = $this->get('search?q=UniqueDevName');

        $result->assertStatus(200);
        $result->assertSee('Developer Test App');
        $result->assertSee('UniqueDevName');
    }

    /**
     * Test: Search works on description
     * 
     * Acceptance Criteria: Search works on app name, developer name, description
     */
    public function testSearchByDescription()
    {
        // Create test app with specific description
        $appModel = model('AppModel');
        $appId = $appModel->insert([
            'name' => 'Description Test App',
            'slug' => 'description-test-app',
            'developer_name' => 'Test Dev',
            'description' => 'This app has a unique keyword: ZebraStripes',
            'platform_type' => 'web',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 70.0,
        ]);

        // Search for the app by description keyword
        $result = $this->get('search?q=ZebraStripes');

        $result->assertStatus(200);
        $result->assertSee('Description Test App');
    }

    /**
     * Test: Search returns results quickly (< 2 seconds)
     * 
     * Acceptance Criteria: Results returned in < 2 seconds
     */
    public function testSearchPerformance()
    {
        // Create multiple test apps
        $appModel = model('AppModel');
        for ($i = 1; $i <= 50; $i++) {
            $appModel->insert([
                'name' => "Performance Test App {$i}",
                'slug' => "performance-test-app-{$i}",
                'developer_name' => "Developer {$i}",
                'description' => "Performance test application number {$i}",
                'platform_type' => 'android',
                'price' => 0,
                'approval_status' => 'approved',
                'trust_score' => 75.0,
            ]);
        }

        // Measure search time
        $startTime = microtime(true);
        $result = $this->get('search?q=Performance');
        $endTime = microtime(true);

        $executionTime = $endTime - $startTime;

        $result->assertStatus(200);
        $this->assertLessThan(2.0, $executionTime, 'Search should complete in less than 2 seconds');
    }

    /**
     * Test: Category filter works correctly
     * 
     * Acceptance Criteria: Filters work correctly
     */
    public function testCategoryFilter()
    {
        // Create category
        $categoryModel = model('CategoryModel');
        $categoryId = $categoryModel->insert([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'display_order' => 1,
        ]);

        // Create app in category
        $appModel = model('AppModel');
        $appId = $appModel->insert([
            'name' => 'Category Filter Test App',
            'slug' => 'category-filter-test-app',
            'developer_name' => 'Test Developer',
            'description' => 'Test app for category filtering',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Attach category
        $db = \Config\Database::connect();
        $db->table('app_categories')->insert([
            'app_id' => $appId,
            'category_id' => $categoryId,
        ]);

        // Create app NOT in category
        $appModel->insert([
            'name' => 'Other Category App',
            'slug' => 'other-category-app',
            'developer_name' => 'Other Developer',
            'description' => 'Test app not in category',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Search with category filter
        $result = $this->get("search?q=Test&category={$categoryId}");

        $result->assertStatus(200);
        $result->assertSee('Category Filter Test App');
        $result->assertDontSee('Other Category App');
    }

    /**
     * Test: Platform filter works correctly
     * 
     * Acceptance Criteria: Filters work correctly
     */
    public function testPlatformFilter()
    {
        // Create apps on different platforms
        $appModel = model('AppModel');
        
        $androidAppId = $appModel->insert([
            'name' => 'Android Test App',
            'slug' => 'android-test-app',
            'developer_name' => 'Test Developer',
            'description' => 'Android application',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        $iosAppId = $appModel->insert([
            'name' => 'iOS Test App',
            'slug' => 'ios-test-app',
            'developer_name' => 'Test Developer',
            'description' => 'iOS application',
            'platform_type' => 'ios',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Search with platform filter
        $result = $this->get('search?q=Test&platform=android');

        $result->assertStatus(200);
        $result->assertSee('Android Test App');
        $result->assertDontSee('iOS Test App');
    }

    /**
     * Test: Price filter works correctly (free apps)
     * 
     * Acceptance Criteria: Filters work correctly
     */
    public function testPriceFilterFree()
    {
        // Create free and paid apps
        $appModel = model('AppModel');
        
        $freeAppId = $appModel->insert([
            'name' => 'Free Test App',
            'slug' => 'free-test-app',
            'developer_name' => 'Test Developer',
            'description' => 'Free application',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        $paidAppId = $appModel->insert([
            'name' => 'Paid Test App',
            'slug' => 'paid-test-app',
            'developer_name' => 'Test Developer',
            'description' => 'Paid application',
            'platform_type' => 'android',
            'price' => 9.99,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Search with free filter
        $result = $this->get('search?q=Test&price_type=free');

        $result->assertStatus(200);
        $result->assertSee('Free Test App');
        $result->assertDontSee('Paid Test App');
    }

    /**
     * Test: Price filter works correctly (paid apps)
     * 
     * Acceptance Criteria: Filters work correctly
     */
    public function testPriceFilterPaid()
    {
        // Create free and paid apps
        $appModel = model('AppModel');
        
        $freeAppId = $appModel->insert([
            'name' => 'Free App',
            'slug' => 'free-app',
            'developer_name' => 'Test Developer',
            'description' => 'Free application',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        $paidAppId = $appModel->insert([
            'name' => 'Paid App',
            'slug' => 'paid-app',
            'developer_name' => 'Test Developer',
            'description' => 'Paid application',
            'platform_type' => 'android',
            'price' => 9.99,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Search with paid filter
        $result = $this->get('search?q=App&price_type=paid');

        $result->assertStatus(200);
        $result->assertSee('Paid App');
        $result->assertDontSee('Free App');
    }

    /**
     * Test: Sorting by relevance works
     * 
     * Acceptance Criteria: Sorting options work
     */
    public function testSortByRelevance()
    {
        // Create apps with different relevance scores
        $appModel = model('AppModel');
        
        // App with keyword in name (highest relevance)
        $appModel->insert([
            'name' => 'UniqueKeyword App',
            'slug' => 'uniquekeyword-app',
            'developer_name' => 'Developer',
            'description' => 'Description',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 50.0,
        ]);

        // App with keyword in developer name (medium relevance)
        $appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app-2',
            'developer_name' => 'UniqueKeyword Developer',
            'description' => 'Description',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 90.0,
        ]);

        // App with keyword in description (lowest relevance)
        $appModel->insert([
            'name' => 'Another App',
            'slug' => 'another-app',
            'developer_name' => 'Developer',
            'description' => 'This has UniqueKeyword in description',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 95.0,
        ]);

        // Search and sort by relevance
        $result = $this->get('search?q=UniqueKeyword&sort=relevance');

        $result->assertStatus(200);
        
        // The app with keyword in name should appear first
        $body = $result->response()->getBody();
        $posName = strpos($body, 'UniqueKeyword App');
        $posDev = strpos($body, 'Test App');
        $posDesc = strpos($body, 'Another App');

        $this->assertLessThan($posDev, $posName, 'Name match should appear before developer match');
        $this->assertLessThan($posDesc, $posDev, 'Developer match should appear before description match');
    }

    /**
     * Test: Sorting by trust score works
     * 
     * Acceptance Criteria: Sorting options work
     */
    public function testSortByTrustScore()
    {
        // Create apps with different trust scores
        $appModel = model('AppModel');
        
        $appModel->insert([
            'name' => 'Low Score App',
            'slug' => 'low-score-app',
            'developer_name' => 'Developer',
            'description' => 'Test app',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 30.0,
        ]);

        $appModel->insert([
            'name' => 'High Score App',
            'slug' => 'high-score-app',
            'developer_name' => 'Developer',
            'description' => 'Test app',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 95.0,
        ]);

        $appModel->insert([
            'name' => 'Medium Score App',
            'slug' => 'medium-score-app',
            'developer_name' => 'Developer',
            'description' => 'Test app',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 60.0,
        ]);

        // Search and sort by trust score (descending)
        $result = $this->get('search?q=App&sort=trust_score&order=DESC');

        $result->assertStatus(200);
        
        // High score app should appear first
        $body = $result->response()->getBody();
        $posHigh = strpos($body, 'High Score App');
        $posMedium = strpos($body, 'Medium Score App');
        $posLow = strpos($body, 'Low Score App');

        $this->assertLessThan($posMedium, $posHigh, 'High score should appear before medium');
        $this->assertLessThan($posLow, $posMedium, 'Medium score should appear before low');
    }

    /**
     * Test: Sorting by date works
     * 
     * Acceptance Criteria: Sorting options work
     */
    public function testSortByDate()
    {
        // Create apps at different times
        $appModel = model('AppModel');
        
        $oldAppId = $appModel->insert([
            'name' => 'Old App',
            'slug' => 'old-app',
            'developer_name' => 'Developer',
            'description' => 'Test app',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
            'created_at' => '2023-01-01 00:00:00',
        ]);

        $newAppId = $appModel->insert([
            'name' => 'New App',
            'slug' => 'new-app',
            'developer_name' => 'Developer',
            'description' => 'Test app',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
            'created_at' => '2024-01-01 00:00:00',
        ]);

        // Search and sort by date (descending - newest first)
        $result = $this->get('search?q=App&sort=date&order=DESC');

        $result->assertStatus(200);
        
        // New app should appear first
        $body = $result->response()->getBody();
        $posNew = strpos($body, 'New App');
        $posOld = strpos($body, 'Old App');

        $this->assertLessThan($posOld, $posNew, 'Newer app should appear before older app');
    }

    /**
     * Test: Search terms are highlighted in results
     * 
     * Acceptance Criteria: Search terms highlighted in results
     */
    public function testSearchTermHighlighting()
    {
        // Create test app
        $appModel = model('AppModel');
        $appModel->insert([
            'name' => 'Highlight Test Application',
            'slug' => 'highlight-test-app',
            'developer_name' => 'Test Developer',
            'description' => 'This is a test application for highlighting',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Search for specific term
        $result = $this->get('search?q=Highlight');

        $result->assertStatus(200);
        
        // Check for highlight markup
        $result->assertSee('<mark class="search-highlight">Highlight</mark>', false);
    }

    /**
     * Test: Pagination works correctly (20 per page)
     * 
     * Acceptance Criteria: Pagination (20 per page)
     */
    public function testPagination()
    {
        // Create 25 test apps
        $appModel = model('AppModel');
        for ($i = 1; $i <= 25; $i++) {
            $appModel->insert([
                'name' => "Pagination Test App {$i}",
                'slug' => "pagination-test-app-{$i}",
                'developer_name' => 'Test Developer',
                'description' => 'Pagination test application',
                'platform_type' => 'android',
                'price' => 0,
                'approval_status' => 'approved',
                'trust_score' => 75.0,
            ]);
        }

        // Get first page
        $result = $this->get('search?q=Pagination');

        $result->assertStatus(200);
        $result->assertSee('25 Results Found');
        
        // Should show apps 1-20
        $result->assertSee('Pagination Test App 1');
        $result->assertSee('Pagination Test App 20');
        $result->assertDontSee('Pagination Test App 21');
        
        // Should have pagination links
        $result->assertSee('Next');

        // Get second page
        $result2 = $this->get('search?q=Pagination&page=2');

        $result2->assertStatus(200);
        
        // Should show apps 21-25
        $result2->assertSee('Pagination Test App 21');
        $result2->assertSee('Pagination Test App 25');
        $result2->assertDontSee('Pagination Test App 20');
        
        // Should have previous link
        $result2->assertSee('Previous');
    }

    /**
     * Test: No results message with suggestions
     * 
     * Acceptance Criteria: Display "no results" message with suggestions
     */
    public function testNoResultsMessage()
    {
        // Search for non-existent app
        $result = $this->get('search?q=NonExistentAppXYZ123');

        $result->assertStatus(200);
        $result->assertSee('No apps found matching your search');
        $result->assertSee('Try searching for:');
    }

    /**
     * Test: Multiple filters work together
     * 
     * Acceptance Criteria: Filters work correctly
     */
    public function testMultipleFilters()
    {
        // Create category
        $categoryModel = model('CategoryModel');
        $categoryId = $categoryModel->insert([
            'name' => 'Multi Filter Category',
            'slug' => 'multi-filter-category',
            'display_order' => 1,
        ]);

        // Create matching app
        $appModel = model('AppModel');
        $matchingAppId = $appModel->insert([
            'name' => 'Matching App',
            'slug' => 'matching-app',
            'developer_name' => 'Test Developer',
            'description' => 'Matches all filters',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Attach category
        $db = \Config\Database::connect();
        $db->table('app_categories')->insert([
            'app_id' => $matchingAppId,
            'category_id' => $categoryId,
        ]);

        // Create non-matching app (different platform)
        $nonMatchingAppId = $appModel->insert([
            'name' => 'Non-Matching App',
            'slug' => 'non-matching-app',
            'developer_name' => 'Test Developer',
            'description' => 'Does not match all filters',
            'platform_type' => 'ios',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Attach same category
        $db->table('app_categories')->insert([
            'app_id' => $nonMatchingAppId,
            'category_id' => $categoryId,
        ]);

        // Search with multiple filters
        $result = $this->get("search?q=App&category={$categoryId}&platform=android&price_type=free");

        $result->assertStatus(200);
        $result->assertSee('Matching App');
        $result->assertDontSee('Non-Matching App');
    }

    /**
     * Test: Only approved apps appear in search results
     * 
     * Acceptance Criteria: Search works correctly
     */
    public function testOnlyApprovedAppsInResults()
    {
        // Create approved app
        $appModel = model('AppModel');
        $approvedAppId = $appModel->insert([
            'name' => 'Approved Search App',
            'slug' => 'approved-search-app',
            'developer_name' => 'Test Developer',
            'description' => 'Approved application',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'approved',
            'trust_score' => 75.0,
        ]);

        // Create pending app
        $pendingAppId = $appModel->insert([
            'name' => 'Pending Search App',
            'slug' => 'pending-search-app',
            'developer_name' => 'Test Developer',
            'description' => 'Pending application',
            'platform_type' => 'android',
            'price' => 0,
            'approval_status' => 'pending',
            'trust_score' => 75.0,
        ]);

        // Search
        $result = $this->get('search?q=Search');

        $result->assertStatus(200);
        $result->assertSee('Approved Search App');
        $result->assertDontSee('Pending Search App');
    }
}
