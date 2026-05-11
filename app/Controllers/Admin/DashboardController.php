<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\AppRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\ScamReportRepository;
use App\Models\UserModel;
use App\Models\NewsletterSubscriberModel;

/**
 * DashboardController
 * 
 * Admin dashboard with platform statistics and metrics.
 * 
 * Features:
 * - Total counts (apps, reviews, scam reports, users, subscribers)
 * - Pending moderation counts
 * - Charts for review and scam report trends (30 days)
 * - Top 10 apps by trust score and views
 * - Recent user registrations (7 days)
 */
class DashboardController extends BaseController
{
    protected AppRepository $appRepository;
    protected ReviewRepository $reviewRepository;
    protected ScamReportRepository $scamReportRepository;
    protected UserModel $userModel;
    protected NewsletterSubscriberModel $subscriberModel;
    
    public function __construct()
    {
        $this->appRepository = new AppRepository();
        $this->reviewRepository = new ReviewRepository();
        $this->scamReportRepository = new ScamReportRepository();
        $this->userModel = new UserModel();
        $this->subscriberModel = new NewsletterSubscriberModel();
    }
    
    /**
     * Display admin dashboard
     * 
     * @return string
     */
    public function index(): string
    {
        // Get total counts
        $totalApps = $this->appRepository->count('approved');
        $totalReviews = $this->reviewRepository->count('approved');
        $totalScamReports = $this->scamReportRepository->count('approved');
        $totalUsers = $this->userModel->where('status', 'active')->countAllResults();
        $totalSubscribers = $this->subscriberModel->where('is_confirmed', 1)->countAllResults();
        
        // Get pending moderation counts
        $pendingReviews = $this->reviewRepository->count('pending');
        $pendingScamReports = $this->scamReportRepository->count('pending');
        $pendingApps = $this->appRepository->count('pending');
        
        // Get review trend data (30 days)
        $reviewTrend = $this->getReviewTrend(30);
        
        // Get scam report trend data (30 days)
        $scamReportTrend = $this->getScamReportTrend(30);
        
        // Get top 10 apps by trust score
        $topAppsByTrustScore = $this->appRepository->getTopByTrustScore(10);
        
        // Get top 10 apps by views
        $topAppsByViews = $this->appRepository->getTopByViews(10);
        
        // Get recent user registrations (7 days)
        $recentUsers = $this->getRecentUsers(7);
        
        $data = [
            'title' => 'Admin Dashboard',
            'totalApps' => $totalApps,
            'totalReviews' => $totalReviews,
            'totalScamReports' => $totalScamReports,
            'totalUsers' => $totalUsers,
            'totalSubscribers' => $totalSubscribers,
            'pendingReviews' => $pendingReviews,
            'pendingScamReports' => $pendingScamReports,
            'pendingApps' => $pendingApps,
            'reviewTrend' => $reviewTrend,
            'scamReportTrend' => $scamReportTrend,
            'topAppsByTrustScore' => $topAppsByTrustScore,
            'topAppsByViews' => $topAppsByViews,
            'recentUsers' => $recentUsers,
        ];
        
        return view('admin/dashboard', $data);
    }
    
    /**
     * Get review submission trend for the past N days
     * 
     * @param int $days
     * @return array
     */
    protected function getReviewTrend(int $days = 30): array
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
            FROM reviews
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", [$days]);
        
        $results = $query->getResultArray();
        
        // Fill in missing dates with zero counts
        $trend = [];
        $startDate = strtotime("-{$days} days");
        
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days", $startDate));
            $trend[$date] = 0;
        }
        
        foreach ($results as $row) {
            $trend[$row['date']] = (int) $row['count'];
        }
        
        return [
            'labels' => array_keys($trend),
            'data' => array_values($trend),
        ];
    }
    
    /**
     * Get scam report submission trend for the past N days
     * 
     * @param int $days
     * @return array
     */
    protected function getScamReportTrend(int $days = 30): array
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
            FROM scam_reports
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", [$days]);
        
        $results = $query->getResultArray();
        
        // Fill in missing dates with zero counts
        $trend = [];
        $startDate = strtotime("-{$days} days");
        
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days", $startDate));
            $trend[$date] = 0;
        }
        
        foreach ($results as $row) {
            $trend[$row['date']] = (int) $row['count'];
        }
        
        return [
            'labels' => array_keys($trend),
            'data' => array_values($trend),
        ];
    }
    
    /**
     * Get recent user registrations
     * 
     * @param int $days
     * @return array
     */
    protected function getRecentUsers(int $days = 7): array
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->userModel->where('created_at >=', $date)
                              ->orderBy('created_at', 'DESC')
                              ->limit(20)
                              ->findAll();
    }
}
