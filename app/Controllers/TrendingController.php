<?php

namespace App\Controllers;

use App\Repositories\AppRepository;
use App\Models\CategoryModel;
use App\Models\ScamReportModel;

/**
 * TrendingController
 * 
 * Handles the trending apps page with comprehensive app listings
 */
class TrendingController extends BaseController
{
    protected AppRepository $appRepository;
    protected CategoryModel $categoryModel;
    protected ScamReportModel $scamReportModel;
    
    public function __construct()
    {
        $this->appRepository = new AppRepository();
        $this->categoryModel = new CategoryModel();
        $this->scamReportModel = new ScamReportModel();
    }
    
    /**
     * Display trending apps page
     * 
     * @return string
     */
    public function index(): string
    {
        // Get trending apps (using existing method)
        $trendingApps = $this->appRepository->getTrending(12);
        
        // Get all categories for filtering
        $categories = $this->categoryModel->getAllOrdered();
        
        // Get recent scam reports (using getAll method with approved status)
        $scamReports = $this->scamReportModel->getAll(['approval_status' => 'approved'], 3);
        
        // Simple pagination (since getTrending only accepts limit)
        $pagination = [
            'current_page' => 1,
            'total_pages' => 1,
            'total' => count($trendingApps),
            'per_page' => 12,
            'has_next' => false,
            'has_prev' => false,
        ];
        
        $data = [
            'title' => 'Trending Apps',
            'trending_apps' => $trendingApps,
            'categories' => $categories,
            'scam_reports' => $scamReports,
            'pagination' => $pagination,
            'current_category' => 'trending',
        ];
        
        return view('trending/index', $data);
    }
    
    /**
     * Filter trending apps by category
     * 
     * @return string
     */
    public function filterByCategory(): string
    {
        // For now, just redirect to main trending page
        // Category filtering can be implemented later when needed
        return $this->index();
    }
}
