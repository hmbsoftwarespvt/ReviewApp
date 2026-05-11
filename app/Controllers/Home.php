<?php

namespace App\Controllers;

use App\Repositories\AppRepository;
use App\Models\CategoryModel;
use App\Models\ReviewModel;
use App\Models\ScamReportModel;
use App\Models\UserModel;

/**
 * HomeController
 * 
 * Handles the public home page with trending apps, categories, and platform statistics.
 */
class Home extends BaseController
{
    protected AppRepository $appRepository;
    protected CategoryModel $categoryModel;
    protected ReviewModel $reviewModel;
    protected ScamReportModel $scamReportModel;
    protected UserModel $userModel;
    
    public function __construct()
    {
        $this->appRepository = new AppRepository();
        $this->categoryModel = new CategoryModel();
        $this->reviewModel = new ReviewModel();
        $this->scamReportModel = new ScamReportModel();
        $this->userModel = new UserModel();
    }
    
    /**
     * Display home page with trending apps and statistics
     * 
     * @return string
     */
    public function index(): string
    {
        // Get trending apps (top 12)
        $trendingApps = $this->appRepository->getTrending(12);
        
        // Get all categories for navigation menu
        $categories = $this->categoryModel->getAllOrdered();
        
        // Get platform statistics
        $statistics = [
            'total_apps' => $this->appRepository->count('approved'),
            'total_reviews' => $this->reviewModel->where('approval_status', 'approved')->countAllResults(),
            'total_scam_reports' => $this->scamReportModel->where('approval_status', 'approved')->countAllResults(),
            'total_users' => $this->userModel->where('status', 'active')->countAllResults(),
        ];
        
        $data = [
            'title' => 'Home',
            'trending_apps' => $trendingApps,
            'categories' => $categories,
            'statistics' => $statistics,
        ];
        
        return view('home', $data);
    }
}
