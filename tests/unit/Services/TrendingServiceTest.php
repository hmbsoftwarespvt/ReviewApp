<?php

namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Services\TrendingService;
use App\Models\AppModel;
use App\Models\ActivityLogModel;
use CodeIgniter\I18n\Time;

/**
 * @internal
 */
final class TrendingServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = 'App';
    
    protected TrendingService $trendingService;
    protected AppModel $appModel;
    protected ActivityLogModel $activityLogModel;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->trendingService = new TrendingService();
        $this->appModel = new AppModel();
        $this->activityLogModel = new ActivityLogModel();
        
        // Clear cache
        cache()->clean();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        cache()->clean();
    }
    
    public function testCalculateTrendingScoreWithHighViews(): void
    {
        // Create app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);
        
        // Create activity log with > 100 views
        $today = Time::now()->toDateString();
        $this->activityLogModel->insert([
            'app_id' => $appId,
            'activity_type' => 'view',
            'activity_date' => $today,
            'count' => 150,
        ]);
        
        $score = $this->trendingService->calculateTrendingScore($appId);
        
        // Should get +10 points for views > 100
        $this->assertEquals(10, $score);
    }
    
    public function testCalculateTrendingScoreWithHighReviews(): void
    {
        // Create app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);
        
        // Create activity log with > 10 reviews
        $today = Time::now()->toDateString();
        $this->activityLogModel->insert([
            'app_id' => $appId,
            'activity_type' => 'review',
            'activity_date' => $today,
            'count' => 15,
        ]);
        
        $score = $this->trendingService->calculateTrendingScore($appId);
        
        // Should get +15 points for reviews > 10
        $this->assertEquals(15, $score);
    }
    
    public function testCalculateTrendingScoreWithHighScamReports(): void
    {
        // Create app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);
        
        // Create activity log with > 5 scam reports
        $today = Time::now()->toDateString();
        $this->activityLogModel->insert([
            'app_id' => $appId,
            'activity_type' => 'scam_report',
            'activity_date' => $today,
            'count' => 8,
        ]);
        
        $score = $this->trendingService->calculateTrendingScore($appId);
        
        // Should get -20 points for scam reports > 5
        $this->assertEquals(-20, $score);
    }
    
    public function testCalculateTrendingScoreCombined(): void
    {
        // Create app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);
        
        // Create activity logs
        $today = Time::now()->toDateString();
        
        $this->activityLogModel->insert([
            'app_id' => $appId,
            'activity_type' => 'view',
            'activity_date' => $today,
            'count' => 200, // +10
        ]);
        
        $this->activityLogModel->insert([
            'app_id' => $appId,
            'activity_type' => 'review',
            'activity_date' => $today,
            'count' => 12, // +15
        ]);
        
        $score = $this->trendingService->calculateTrendingScore($appId);
        
        // Should get +10 (views) + 15 (reviews) = 25
        $this->assertEquals(25, $score);
    }
    
    public function testCalculateTrendingScoreWithNoActivity(): void
    {
        // Create app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);
        
        $score = $this->trendingService->calculateTrendingScore($appId);
        
        // Should get 0 points with no activity
        $this->assertEquals(0, $score);
    }
    
    public function testGetTrendingAppsReturnsTopApps(): void
    {
        // Create multiple apps with different trending scores
        $app1Id = $this->appModel->insert([
            'name' => 'App 1',
            'slug' => 'app-1',
            'platform_type' => 'android',
            'developer_name' => 'Developer 1',
            'approval_status' => 'approved',
            'trending_score' => 25,
        ]);
        
        $app2Id = $this->appModel->insert([
            'name' => 'App 2',
            'slug' => 'app-2',
            'platform_type' => 'android',
            'developer_name' => 'Developer 2',
            'approval_status' => 'approved',
            'trending_score' => 15,
        ]);
        
        $app3Id = $this->appModel->insert([
            'name' => 'App 3',
            'slug' => 'app-3',
            'platform_type' => 'android',
            'developer_name' => 'Developer 3',
            'approval_status' => 'approved',
            'trending_score' => 10,
        ]);
        
        $trendingApps = $this->trendingService->getTrendingApps(2);
        
        $this->assertCount(2, $trendingApps);
        $this->assertEquals('App 1', $trendingApps[0]['name']);
        $this->assertEquals('App 2', $trendingApps[1]['name']);
    }
    
    public function testGetTrendingAppsCachesResults(): void
    {
        // Create app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
            'trending_score' => 20,
        ]);
        
        // First call - should cache
        $apps1 = $this->trendingService->getTrendingApps();
        
        // Second call - should use cache
        $apps2 = $this->trendingService->getTrendingApps();
        
        $this->assertEquals($apps1, $apps2);
        
        // Verify cache exists
        $cached = cache()->get('trending_apps');
        $this->assertNotNull($cached);
    }
    
    public function testTrackViewIncrementsActivityLog(): void
    {
        // Create app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);
        
        // Track view
        $this->trendingService->trackView($appId);
        
        // Verify activity log
        $today = Time::now()->toDateString();
        $activity = $this->activityLogModel
            ->where('app_id', $appId)
            ->where('activity_type', 'view')
            ->where('activity_date', $today)
            ->first();
        
        $this->assertNotNull($activity);
        $this->assertEquals(1, $activity['count']);
        
        // Track another view
        $this->trendingService->trackView($appId);
        
        // Verify count incremented
        $activity = $this->activityLogModel
            ->where('app_id', $appId)
            ->where('activity_type', 'view')
            ->where('activity_date', $today)
            ->first();
        
        $this->assertEquals(2, $activity['count']);
    }
    
    public function testUpdateDailyTrendingUpdatesAllApps(): void
    {
        // Create multiple apps
        $app1Id = $this->appModel->insert([
            'name' => 'App 1',
            'slug' => 'app-1',
            'platform_type' => 'android',
            'developer_name' => 'Developer 1',
            'approval_status' => 'approved',
        ]);
        
        $app2Id = $this->appModel->insert([
            'name' => 'App 2',
            'slug' => 'app-2',
            'platform_type' => 'android',
            'developer_name' => 'Developer 2',
            'approval_status' => 'approved',
        ]);
        
        // Add activity for app1
        $today = Time::now()->toDateString();
        $this->activityLogModel->insert([
            'app_id' => $app1Id,
            'activity_type' => 'view',
            'activity_date' => $today,
            'count' => 150,
        ]);
        
        // Update trending
        $count = $this->trendingService->updateDailyTrending();
        
        $this->assertEquals(2, $count);
        
        // Verify app1 has trending score
        $app1 = $this->appModel->find($app1Id);
        $this->assertEquals(10, $app1['trending_score']);
        
        // Verify app2 has 0 trending score
        $app2 = $this->appModel->find($app2Id);
        $this->assertEquals(0, $app2['trending_score']);
    }
}
