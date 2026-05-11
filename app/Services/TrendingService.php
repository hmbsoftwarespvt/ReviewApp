<?php

namespace App\Services;

use App\Models\AppModel;
use App\Models\ActivityLogModel;
use CodeIgniter\I18n\Time;

/**
 * TrendingService
 * 
 * Calculates and manages trending apps based on 24-hour activity metrics.
 * 
 * Trending Score Formula:
 * - Views > 100 in 24h: +10 points
 * - Reviews > 10 in 24h: +15 points
 * - Scam Reports > 5 in 24h: -20 points
 */
class TrendingService
{
    protected AppModel $appModel;
    protected ActivityLogModel $activityLogModel;
    protected $cache;
    
    public function __construct()
    {
        $this->appModel = new AppModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->cache = \Config\Services::cache();
    }
    
    /**
     * Calculate trending score for a specific app
     * 
     * @param int $appId
     * @return float Trending score
     */
    public function calculateTrendingScore(int $appId): float
    {
        $app = $this->appModel->find($appId);
        
        if (!$app) {
            return 0.0;
        }
        
        // Get 24-hour metrics
        $yesterday = Time::now()->subDays(1)->toDateString();
        $today = Time::now()->toDateString();
        
        $metrics = $this->get24HourMetrics($appId, $yesterday, $today);
        
        // Calculate trending score
        $score = 0;
        
        // Views > 100: +10 points
        if ($metrics['views'] > 100) {
            $score += 10;
        }
        
        // Reviews > 10: +15 points
        if ($metrics['reviews'] > 10) {
            $score += 15;
        }
        
        // Scam reports > 5: -20 points
        if ($metrics['scam_reports'] > 5) {
            $score -= 20;
        }
        
        return (float) $score;
    }
    
    /**
     * Update daily trending scores for all apps
     * This method should be run as a scheduled job at 00:00 UTC
     * 
     * @return int Number of apps updated
     */
    public function updateDailyTrending(): int
    {
        $apps = $this->appModel->where('approval_status', 'approved')->findAll();
        $count = 0;
        
        foreach ($apps as $app) {
            $trendingScore = $this->calculateTrendingScore($app['id']);
            
            // Update app's trending score
            $this->appModel->update($app['id'], [
                'trending_score' => $trendingScore
            ]);
            
            // Store in activity logs for historical tracking
            $this->storeTrendingScore($app['id'], $trendingScore);
            
            $count++;
        }
        
        // Invalidate trending cache
        $this->cache->delete('trending_apps');
        
        return $count;
    }
    
    /**
     * Get trending apps
     * 
     * @param int $limit Number of apps to return (default: 12)
     * @return array List of trending apps
     */
    public function getTrendingApps(int $limit = 12): array
    {
        // Check cache first
        $cacheKey = 'trending_apps';
        $cachedApps = $this->cache->get($cacheKey);
        
        if ($cachedApps !== null) {
            return array_slice($cachedApps, 0, $limit);
        }
        
        // Get apps sorted by trending score
        $apps = $this->appModel
            ->where('approval_status', 'approved')
            ->orderBy('trending_score', 'DESC')
            ->orderBy('trust_score', 'DESC') // Secondary sort by trust score
            ->limit(50) // Get top 50 for caching
            ->findAll();
        
        // Cache for 1 hour
        $this->cache->save($cacheKey, $apps, 3600);
        
        return array_slice($apps, 0, $limit);
    }
    
    /**
     * Get 24-hour metrics for an app
     * 
     * @param int $appId
     * @param string $startDate
     * @param string $endDate
     * @return array Metrics (views, reviews, scam_reports)
     */
    protected function get24HourMetrics(int $appId, string $startDate, string $endDate): array
    {
        $metrics = [
            'views' => 0,
            'reviews' => 0,
            'scam_reports' => 0,
        ];
        
        // Get activity logs for the date range
        $activities = $this->activityLogModel
            ->where('app_id', $appId)
            ->where('activity_date >=', $startDate)
            ->where('activity_date <=', $endDate)
            ->findAll();
        
        foreach ($activities as $activity) {
            switch ($activity['activity_type']) {
                case 'view':
                    $metrics['views'] += $activity['count'];
                    break;
                case 'review':
                    $metrics['reviews'] += $activity['count'];
                    break;
                case 'scam_report':
                    $metrics['scam_reports'] += $activity['count'];
                    break;
            }
        }
        
        return $metrics;
    }
    
    /**
     * Store trending score in activity logs
     * 
     * @param int $appId
     * @param float $score
     * @return void
     */
    protected function storeTrendingScore(int $appId, float $score): void
    {
        $today = Time::now()->toDateString();
        
        // Store as a special activity type for historical tracking
        $existing = $this->activityLogModel
            ->where('app_id', $appId)
            ->where('activity_type', 'trending_score')
            ->where('activity_date', $today)
            ->first();
        
        if ($existing) {
            $this->activityLogModel->update($existing['id'], [
                'count' => (int) $score
            ]);
        } else {
            $this->activityLogModel->insert([
                'app_id' => $appId,
                'activity_type' => 'trending_score',
                'activity_date' => $today,
                'count' => (int) $score
            ]);
        }
    }
    
    /**
     * Track app view activity
     * 
     * @param int $appId
     * @return void
     */
    public function trackView(int $appId): void
    {
        $this->trackActivity($appId, 'view');
    }
    
    /**
     * Track review submission activity
     * 
     * @param int $appId
     * @return void
     */
    public function trackReview(int $appId): void
    {
        $this->trackActivity($appId, 'review');
    }
    
    /**
     * Track scam report submission activity
     * 
     * @param int $appId
     * @return void
     */
    public function trackScamReport(int $appId): void
    {
        $this->trackActivity($appId, 'scam_report');
    }
    
    /**
     * Track activity in activity_logs table
     * 
     * @param int $appId
     * @param string $activityType
     * @return void
     */
    protected function trackActivity(int $appId, string $activityType): void
    {
        $today = Time::now()->toDateString();
        
        $existing = $this->activityLogModel
            ->where('app_id', $appId)
            ->where('activity_type', $activityType)
            ->where('activity_date', $today)
            ->first();
        
        if ($existing) {
            // Increment count
            $this->activityLogModel->update($existing['id'], [
                'count' => $existing['count'] + 1
            ]);
        } else {
            // Create new record
            $this->activityLogModel->insert([
                'app_id' => $appId,
                'activity_type' => $activityType,
                'activity_date' => $today,
                'count' => 1
            ]);
        }
    }
}
