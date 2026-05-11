<?php

namespace App\Services;

use App\Models\AppModel;
use App\Models\ScamReportModel;

/**
 * DeveloperReputationService
 * 
 * Calculates developer reputation component (0-20 points) based on:
 * - Total app count (1-5 points)
 * - Average trust score across all apps (2-10 points)
 * - Total scam reports across all apps (-5 points if > 20)
 */
class DeveloperReputationService
{
    protected AppModel $appModel;
    protected ScamReportModel $scamReportModel;
    
    public function __construct()
    {
        $this->appModel = new AppModel();
        $this->scamReportModel = new ScamReportModel();
    }
    
    /**
     * Calculate developer reputation for an app
     * 
     * @param int $appId
     * @return float Developer reputation score (0-20)
     */
    public function calculateReputation(int $appId): float
    {
        $app = $this->appModel->find($appId);
        
        if (!$app) {
            return 0.0;
        }
        
        $developerName = $app['developer_name'];
        
        return $this->calculateReputationByDeveloper($developerName);
    }
    
    /**
     * Calculate developer reputation by developer name
     * 
     * @param string $developerName
     * @return float Developer reputation score (0-20)
     */
    public function calculateReputationByDeveloper(string $developerName): float
    {
        $stats = $this->getDeveloperStats($developerName);
        
        $score = 0.0;
        
        // 1. App count contribution (1-5 points)
        if ($stats['app_count'] > 10) {
            $score += 5;
        } elseif ($stats['app_count'] >= 5) {
            $score += 3;
        } else {
            $score += 1;
        }
        
        // 2. Average trust score contribution (2-10 points)
        $avgTrustScore = $stats['average_trust_score'];
        
        if ($avgTrustScore > 80) {
            $score += 10;
        } elseif ($avgTrustScore >= 60) {
            $score += 6;
        } else {
            $score += 2;
        }
        
        // 3. Scam report penalty (-5 points if > 20)
        if ($stats['total_scam_reports'] > 20) {
            $score -= 5;
        }
        
        // Ensure score is between 0 and 20
        $score = max(0, min(20, $score));
        
        // Update all apps by this developer
        $this->updateDeveloperApps($developerName, $score);
        
        return $score;
    }
    
    /**
     * Get developer statistics
     * 
     * @param string $developerName
     * @return array
     */
    public function getDeveloperStats(string $developerName): array
    {
        $apps = $this->appModel->getByDeveloper($developerName);
        
        $appCount = count($apps);
        $totalTrustScore = 0;
        $totalScamReports = 0;
        
        foreach ($apps as $app) {
            $totalTrustScore += $app['trust_score'] ?? 0;
            $totalScamReports += $this->scamReportModel->getCountByApp($app['id'], 'approved');
        }
        
        $averageTrustScore = $appCount > 0 ? $totalTrustScore / $appCount : 0;
        
        return [
            'developer_name' => $developerName,
            'app_count' => $appCount,
            'average_trust_score' => round($averageTrustScore, 2),
            'total_scam_reports' => $totalScamReports,
            'apps' => $apps,
        ];
    }
    
    /**
     * Get average trust score for developer
     * 
     * @param string $developerName
     * @return float
     */
    public function getAverageTrustScore(string $developerName): float
    {
        $stats = $this->getDeveloperStats($developerName);
        return $stats['average_trust_score'];
    }
    
    /**
     * Get detailed reputation breakdown
     * 
     * @param string $developerName
     * @return array
     */
    public function getReputationBreakdown(string $developerName): array
    {
        $stats = $this->getDeveloperStats($developerName);
        
        $breakdown = [];
        
        // App count contribution
        if ($stats['app_count'] > 10) {
            $breakdown['app_count'] = ['points' => 5, 'label' => 'Many apps published (> 10)'];
        } elseif ($stats['app_count'] >= 5) {
            $breakdown['app_count'] = ['points' => 3, 'label' => 'Several apps published (5-10)'];
        } else {
            $breakdown['app_count'] = ['points' => 1, 'label' => 'Few apps published (< 5)'];
        }
        
        // Average trust score contribution
        $avgScore = $stats['average_trust_score'];
        if ($avgScore > 80) {
            $breakdown['average_trust_score'] = ['points' => 10, 'label' => 'Excellent average trust score (> 80)'];
        } elseif ($avgScore >= 60) {
            $breakdown['average_trust_score'] = ['points' => 6, 'label' => 'Good average trust score (60-80)'];
        } else {
            $breakdown['average_trust_score'] = ['points' => 2, 'label' => 'Low average trust score (< 60)'];
        }
        
        // Scam report penalty
        if ($stats['total_scam_reports'] > 20) {
            $breakdown['scam_reports'] = [
                'points' => -5,
                'label' => "Many scam reports ({$stats['total_scam_reports']} total)",
            ];
        }
        
        return [
            'score' => $this->calculateReputationByDeveloper($developerName),
            'stats' => $stats,
            'breakdown' => $breakdown,
        ];
    }
    
    /**
     * Update developer reputation for all apps by developer
     * 
     * @param string $developerName
     * @param float $reputation
     * @return int Number of apps updated
     */
    protected function updateDeveloperApps(string $developerName, float $reputation): int
    {
        $apps = $this->appModel->getByDeveloper($developerName);
        $count = 0;
        
        foreach ($apps as $app) {
            $this->appModel->update($app['id'], ['developer_reputation' => $reputation]);
            $count++;
        }
        
        return $count;
    }
    
    /**
     * Recalculate reputation for all developers
     * 
     * @return array Statistics about the recalculation
     */
    public function recalculateAllReputations(): array
    {
        $db = \Config\Database::connect();
        
        // Get all unique developer names
        $query = $db->query("SELECT DISTINCT developer_name FROM apps WHERE approval_status = 'approved'");
        $developers = $query->getResultArray();
        
        $stats = [
            'total_developers' => count($developers),
            'apps_updated' => 0,
        ];
        
        foreach ($developers as $dev) {
            $reputation = $this->calculateReputationByDeveloper($dev['developer_name']);
            $stats['apps_updated'] += $this->updateDeveloperApps($dev['developer_name'], $reputation);
        }
        
        return $stats;
    }
}
