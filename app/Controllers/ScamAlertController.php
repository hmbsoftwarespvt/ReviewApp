<?php

namespace App\Controllers;

use App\Repositories\ScamReportRepository;
use App\Models\CategoryModel;

/**
 * ScamAlertController
 * 
 * Handles public scam alerts page with filtering capabilities.
 * Displays all approved scam reports with category, risk level, and status filters.
 */
class ScamAlertController extends BaseController
{
    protected ScamReportRepository $scamReportRepository;
    protected CategoryModel $categoryModel;
    
    public function __construct()
    {
        $this->scamReportRepository = new ScamReportRepository();
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * Display scam alerts page with filtering
     * 
     * @return string
     */
    public function index(): string
    {
        // Get filter parameters from query string
        $filters = [
            'approval_status' => 'approved', // Always show only approved reports on public site
        ];
        
        // Risk level filter
        $riskLevel = $this->request->getGet('risk_level');
        if ($riskLevel && in_array($riskLevel, ['low', 'medium', 'high'])) {
            $filters['risk_level'] = $riskLevel;
        }
        
        // Category filter
        $categoryId = $this->request->getGet('category');
        if ($categoryId && is_numeric($categoryId)) {
            $filters['category_id'] = (int)$categoryId;
        }
        
        // Get current page
        $page = (int)($this->request->getGet('page') ?? 1);
        $page = max(1, $page); // Ensure page is at least 1
        
        // Get scam reports with filters (20 per page as per requirements)
        $result = $this->getScamReportsWithFilters($filters, $page, 20);
        
        // Get all categories for filter dropdown
        $categories = $this->categoryModel->getAllOrdered();
        
        // Prepare view data
        $data = [
            'title' => 'Scam Alerts',
            'scam_reports' => $result['data'],
            'pagination' => $result['pagination'],
            'categories' => $categories,
            'filters' => [
                'risk_level' => $riskLevel ?? '',
                'category' => $categoryId ?? '',
            ],
        ];
        
        return view('scam_alerts', $data);
    }
    
    /**
     * Get scam reports with filters including category
     * 
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    protected function getScamReportsWithFilters(array $filters, int $page, int $perPage): array
    {
        $db = \Config\Database::connect();
        $offset = ($page - 1) * $perPage;
        
        // Build query with joins
        $builder = $db->table('scam_reports')
                     ->select('scam_reports.*, apps.name as app_name, apps.slug as app_slug, users.username')
                     ->join('apps', 'apps.id = scam_reports.app_id')
                     ->join('users', 'users.id = scam_reports.user_id');
        
        // Apply approval status filter (always approved for public)
        if (!empty($filters['approval_status'])) {
            $builder->where('scam_reports.approval_status', $filters['approval_status']);
        }
        
        // Apply risk level filter
        if (!empty($filters['risk_level'])) {
            $builder->where('scam_reports.risk_level', $filters['risk_level']);
        }
        
        // Apply category filter (join with app_categories)
        if (!empty($filters['category_id'])) {
            $builder->join('app_categories', 'app_categories.app_id = apps.id')
                   ->where('app_categories.category_id', $filters['category_id']);
        }
        
        // Get total count for pagination
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);
        
        // Get paginated results sorted by date (descending)
        $reports = $builder->orderBy('scam_reports.created_at', 'DESC')
                          ->limit($perPage, $offset)
                          ->get()
                          ->getResultArray();
        
        return [
            'data' => $reports,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Get risk level badge class
     * 
     * @param string $riskLevel
     * @return string
     */
    public static function getRiskBadgeClass(string $riskLevel): string
    {
        return match($riskLevel) {
            'high' => 'bg-danger',
            'medium' => 'bg-warning',
            'low' => 'bg-success',
            default => 'bg-secondary',
        };
    }
}
