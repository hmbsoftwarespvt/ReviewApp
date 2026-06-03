<?php

namespace App\Services;

use App\Models\AppModel;
use App\Models\ReviewModel;
use App\Models\ScamReportModel;
use App\Models\SettingModel;
use CodeIgniter\I18n\Time;

/**
 * TrustScoreService
 * 
 * Calculates trust scores for apps using a configurable weighted algorithm.
 * 
 * Components:
 * 1. User Review Ratings (30%)
 * 2. Security Score (25%)
 * 3. Developer Reputation (20%)
 * 4. Scam Report Count (15%)
 * 5. App Age (10%)
 */
class TrustScoreService
{
    protected AppModel $appModel;
    protected ReviewModel $reviewModel;
    protected ScamReportModel $scamReportModel;
    protected SettingModel $settingModel;
    protected $cache;
    
    protected array $defaultWeights = [
        'review_rating' => 30,
        'security_score' => 25,
        'developer_reputation' => 20,
        'scam_report_count' => 15,
        'app_age' => 10,
    ];
    
    public function __construct()
    {
        $this->appModel = new AppModel();
        $this->reviewModel = new ReviewModel();
        $this->scamReportModel = new ScamReportModel();
        $this->settingModel = new SettingModel();
        $this->cache = \Config\Services::cache();
    }
    
    /**
     * Calculate trust score for an app
     * 
     * @param int $appId
     * @return float Trust score (0-100)
     */
    public function calculateTrustScore(int $appId): float
    {
        // Check cache first
        $cacheKey = "trust_score_{$appId}";
        $cachedScore = $this->cache->get($cacheKey);
        
        if ($cachedScore !== null) {
            return (float) $cachedScore;
        }
        
        // Get app data
        $app = $this->appModel->find($appId);
        
        if (!$app) {
            return 0.0;
        }
        
        // Calculate components
        $breakdown = $this->getTrustScoreBreakdown($appId);
        
        // Sum all components
        $totalScore = $breakdown['review_rating']['score']
                    + $breakdown['security_score']['score']
                    + $breakdown['developer_reputation']['score']
                    + $breakdown['scam_report_count']['score']
                    + $breakdown['app_age']['score'];
        
        // Ensure score is between 0 and 100
        $totalScore = max(0, min(100, $totalScore));
        
        // Cache for 5 minutes
        $this->cache->save($cacheKey, $totalScore, 300);
        
        // Update app record
        $this->appModel->update($appId, ['trust_score' => $totalScore]);
        
        return $totalScore;
    }
    
    /**
     * Get trust score breakdown showing individual component contributions
     * 
     * @param int $appId
     * @return array Breakdown of all components
     */
    public function getTrustScoreBreakdown(int $appId): array
    {
        $app = $this->appModel->find($appId);
        
        if (!$app) {
            return $this->getEmptyBreakdown();
        }
        
        // Load weights from settings (or use defaults)
        $weights = $this->loadWeights();
        
        return [
            'review_rating' => $this->calculateReviewRatingScore($appId, $weights['review_rating']),
            'security_score' => $this->getSecurityScore($appId, $weights['security_score']),
            'developer_reputation' => $this->getDeveloperReputation($appId, $weights['developer_reputation']),
            'scam_report_count' => $this->calculateScamReportScore($appId, $weights['scam_report_count']),
            'app_age' => $this->calculateAppAgeScore($app, $weights['app_age']),
        ];
    }
    
    /**
     * Get color classification for trust score
     * 
     * @param float $score
     * @return string Color name (green, yellow, red)
     */
    public function getScoreColor(float $score): string
    {
        if ($score >= 80) {
            return 'green';
        } elseif ($score >= 50) {
            return 'yellow';
        } else {
            return 'red';
        }
    }
    
    /**
     * Get CSS class for trust score color
     * 
     * @param float $score
     * @return string CSS class name
     */
    public function getScoreColorClass(float $score): string
    {
        $color = $this->getScoreColor($score);
        
        return match($color) {
            'green' => 'text-success',
            'yellow' => 'text-warning',
            'red' => 'text-danger',
            default => 'text-secondary',
        };
    }
    
    /**
     * Invalidate cached trust score
     * 
     * @param int $appId
     * @return bool
     */
    public function invalidateCache(int $appId): bool
    {
        $cacheKey = "trust_score_{$appId}";
        return $this->cache->delete($cacheKey);
    }
    
    /**
     * Recalculate all app trust scores
     * 
     * @return int Number of apps updated
     */
    public function recalculateAllScores(): int
    {
        $apps = $this->appModel->where('approval_status', 'approved')->findAll();
        $count = 0;
        
        foreach ($apps as $app) {
            $this->invalidateCache($app['id']);
            $this->calculateTrustScore($app['id']);
            $count++;
        }
        
        return $count;
    }
    
    // ========== Protected Helper Methods ==========
    
    /**
     * Calculate review rating component score
     */
    protected function calculateReviewRatingScore(int $appId, float $maxPoints): array
    {
        $avgRating = $this->reviewModel->getAverageRating($appId);
        $reviewCount = $this->reviewModel->getReviewCount($appId, 'approved');
        
        // Determine score based on average rating
        $score = 0;
        
        if ($avgRating >= 4.5) {
            $score = $maxPoints; // 30 points
        } elseif ($avgRating >= 3.5) {
            $score = $maxPoints * 0.733; // 22 points (73.3% of 30)
        } elseif ($avgRating >= 2.5) {
            $score = $maxPoints * 0.5; // 15 points (50% of 30)
        } elseif ($avgRating >= 1.5) {
            $score = $maxPoints * 0.267; // 8 points (26.7% of 30)
        } else {
            $score = 0;
        }
        
        return [
            'score' => round($score, 2),
            'max_points' => $maxPoints,
            'average_rating' => round($avgRating, 2),
            'review_count' => $reviewCount,
            'label' => 'User Reviews',
        ];
    }
    
    /**
     * Get security score component
     */
    protected function getSecurityScore(int $appId, float $maxPoints): array
    {
        $app = $this->appModel->find($appId);
        $score = $app['security_score'] ?? 0;
        
        // Security score is already calculated (0-25)
        // Just ensure it doesn't exceed max points
        $score = min($score, $maxPoints);
        
        return [
            'score' => round($score, 2),
            'max_points' => $maxPoints,
            'label' => 'Security Analysis',
        ];
    }
    
    /**
     * Get developer reputation component
     */
    protected function getDeveloperReputation(int $appId, float $maxPoints): array
    {
        $app = $this->appModel->find($appId);
        $score = $app['developer_reputation'] ?? 0;
        
        // Developer reputation is already calculated (0-20)
        // Just ensure it doesn't exceed max points
        $score = min($score, $maxPoints);
        
        return [
            'score' => round($score, 2),
            'max_points' => $maxPoints,
            'label' => 'Developer Reputation',
        ];
    }
    
    /**
     * Calculate scam report count component score
     */
    protected function calculateScamReportScore(int $appId, float $maxPoints): array
    {
        $scamReportCount = $this->scamReportModel->getCountByApp($appId, 'approved');
        
        // Determine score based on scam report count
        $score = 0;
        
        if ($scamReportCount === 0) {
            $score = $maxPoints; // 15 points
        } elseif ($scamReportCount <= 5) {
            $score = $maxPoints * 0.667; // 10 points (66.7% of 15)
        } elseif ($scamReportCount <= 15) {
            $score = $maxPoints * 0.333; // 5 points (33.3% of 15)
        } else {
            $score = 0;
        }
        
        return [
            'score' => round($score, 2),
            'max_points' => $maxPoints,
            'scam_report_count' => $scamReportCount,
            'label' => 'Scam Reports',
        ];
    }
    
    /**
     * Calculate app age component score
     */
    protected function calculateAppAgeScore(array $app, float $maxPoints): array
    {
        if (empty($app['release_date']) || !strtotime($app['release_date'])) {
            return [
                'score' => 0,
                'max_points' => $maxPoints,
                'age_days' => 0,
                'label' => 'App Age',
            ];
        }
        
        $releaseDate = Time::parse($app['release_date']);
        $now = Time::now();
        $ageDays = $now->difference($releaseDate)->getDays();
        
        // Determine score based on age
        $score = 0;
        
        if ($ageDays > 365) {
            $score = $maxPoints; // 10 points
        } elseif ($ageDays >= 180) {
            $score = $maxPoints * 0.7; // 7 points (70% of 10)
        } elseif ($ageDays >= 90) {
            $score = $maxPoints * 0.4; // 4 points (40% of 10)
        } else {
            $score = $maxPoints * 0.2; // 2 points (20% of 10)
        }
        
        return [
            'score' => round($score, 2),
            'max_points' => $maxPoints,
            'age_days' => $ageDays,
            'label' => 'App Age',
        ];
    }
    
    /**
     * Load algorithm weights from settings
     */
    protected function loadWeights(): array
    {
        $weights = $this->settingModel->getByPrefix('trust_algorithm_');
        
        if (empty($weights)) {
            return $this->defaultWeights;
        }
        
        return [
            'review_rating' => $weights['trust_algorithm_review_rating'] ?? $this->defaultWeights['review_rating'],
            'security_score' => $weights['trust_algorithm_security_score'] ?? $this->defaultWeights['security_score'],
            'developer_reputation' => $weights['trust_algorithm_developer_reputation'] ?? $this->defaultWeights['developer_reputation'],
            'scam_report_count' => $weights['trust_algorithm_scam_report_count'] ?? $this->defaultWeights['scam_report_count'],
            'app_age' => $weights['trust_algorithm_app_age'] ?? $this->defaultWeights['app_age'],
        ];
    }
    
    /**
     * Get empty breakdown structure
     */
    protected function getEmptyBreakdown(): array
    {
        return [
            'review_rating' => ['score' => 0, 'max_points' => 30, 'label' => 'User Reviews'],
            'security_score' => ['score' => 0, 'max_points' => 25, 'label' => 'Security Analysis'],
            'developer_reputation' => ['score' => 0, 'max_points' => 20, 'label' => 'Developer Reputation'],
            'scam_report_count' => ['score' => 0, 'max_points' => 15, 'label' => 'Scam Reports'],
            'app_age' => ['score' => 0, 'max_points' => 10, 'label' => 'App Age'],
        ];
    }
}
