<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Repositories\AppRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\ScamReportRepository;
use App\Services\TrustScoreService;
use App\Services\RecommendationService;
use App\Models\ScreenshotModel;

/**
 * AppController
 * 
 * Handles public-facing app detail pages and related functionality.
 */
class AppController extends BaseController
{
    protected AppRepository $appRepository;
    protected ReviewRepository $reviewRepository;
    protected ScamReportRepository $scamReportRepository;
    protected TrustScoreService $trustScoreService;
    protected RecommendationService $recommendationService;
    protected ScreenshotModel $screenshotModel;
    
    public function __construct()
    {
        $this->appRepository = new AppRepository();
        $this->reviewRepository = new ReviewRepository();
        $this->scamReportRepository = new ScamReportRepository();
        $this->trustScoreService = new TrustScoreService();
        $this->recommendationService = new RecommendationService();
        $this->screenshotModel = new ScreenshotModel();
    }
    
    /**
     * Display app detail page
     * 
     * @param string $slug App slug
     * @return string
     */
    public function show(string $slug)
    {
        // Find app by slug
        $app = $this->appRepository->findBySlug($slug);
        
        if (!$app) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "App not found: {$slug}"
            );
        }
        
        // Check if app is approved (unless user is admin)
        if ($app['approval_status'] !== 'approved' && !session()->get('is_admin')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "App not found: {$slug}"
            );
        }
        
        // Increment view count
        $this->appRepository->incrementViewCount($app['id']);
        
        // Get trust score breakdown
        $trustScoreBreakdown = $this->trustScoreService->getTrustScoreBreakdown($app['id']);
        
        // Get trust score color
        $trustScoreColor = $this->trustScoreService->getScoreColor((float)$app['trust_score']);
        $trustScoreColorClass = $this->trustScoreService->getScoreColorClass((float)$app['trust_score']);
        
        // Get app categories
        $categories = $this->appRepository->find($app['id']);
        $appCategories = $this->appRepository->getWithDetails($app['id'])['categories'] ?? [];
        
        // Get screenshots
        $screenshots = $this->screenshotModel->where('app_id', $app['id'])
                                            ->orderBy('display_order', 'ASC')
                                            ->findAll();
        
        // Get approved reviews with pagination
        $page = (int)($this->request->getGet('review_page') ?? 1);
        $reviews = $this->reviewRepository->getByApp($app['id'], 'approved', $page);
        
        // Get average rating and review count
        $averageRating = $this->reviewRepository->getAverageRating($app['id']);
        $reviewCount = $this->reviewRepository->getReviewCount($app['id'], 'approved');
        
        // Get approved scam reports with pagination
        $scamPage = (int)($this->request->getGet('scam_page') ?? 1);
        $scamReports = $this->scamReportRepository->getByApp($app['id'], 'approved', $scamPage);
        
        // Get scam report counts by risk level
        $scamReportCounts = [
            'high' => $this->scamReportRepository->getCountByRiskLevel($app['id'], 'high'),
            'medium' => $this->scamReportRepository->getCountByRiskLevel($app['id'], 'medium'),
            'low' => $this->scamReportRepository->getCountByRiskLevel($app['id'], 'low'),
        ];
        $totalScamReports = array_sum($scamReportCounts);
        
        // Get similar apps
        $similarApps = $this->recommendationService->getSimilarApps($app['id'], 6);
        
        // Check if current user has already reviewed this app
        $userHasReviewed = false;
        $userPendingReview = null;
        $userPendingScamReport = null;
        if (session()->get('isLoggedIn')) {
            $userId = session()->get('user_id');
            $userHasReviewed = $this->reviewRepository->userHasReviewed($userId, $app['id']);
            
            // Get user's pending review if exists
            if ($userHasReviewed) {
                $db = \Config\Database::connect();
                $userPendingReview = $db->table('reviews')
                    ->where('user_id', $userId)
                    ->where('app_id', $app['id'])
                    ->where('approval_status', 'pending')
                    ->get()
                    ->getRowArray();
            }
            
            // Get user's pending scam report if exists
            $db = \Config\Database::connect();
            $userPendingScamReport = $db->table('scam_reports')
                ->where('user_id', $userId)
                ->where('app_id', $app['id'])
                ->where('approval_status', 'pending')
                ->get()
                ->getRowArray();
        }
        
        // Prepare view data
        $data = [
            'title' => $app['name'],
            'app' => $app,
            'categories' => $appCategories,
            'trustScoreBreakdown' => $trustScoreBreakdown,
            'trustScoreColor' => $trustScoreColor,
            'trustScoreColorClass' => $trustScoreColorClass,
            'screenshots' => $screenshots,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
            'scamReports' => $scamReports,
            'scamReportCounts' => $scamReportCounts,
            'totalScamReports' => $totalScamReports,
            'similarApps' => $similarApps,
            'userHasReviewed' => $userHasReviewed,
            'userPendingReview' => $userPendingReview,
            'userPendingScamReport' => $userPendingScamReport,
        ];
        
        return view('app_detail', $data);
    }
    
    /**
     * Submit a review for an app
     * 
     * @param int $appId App ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function submitReview(int $appId)
    {
        // Check if user is authenticated
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login')
                           ->with('error', 'You must be logged in to submit a review.');
        }
        
        $userId = session()->get('user_id');
        
        // Check if app exists
        $app = $this->appRepository->find($appId);
        if (!$app) {
            return redirect()->back()
                           ->with('error', 'App not found.');
        }
        
        // Check for duplicate review
        if ($this->reviewRepository->userHasReviewed($userId, $appId)) {
            return redirect()->back()
                           ->with('error', 'You have already submitted a review for this app.');
        }
        
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'rating' => 'required|integer|greater_than[0]|less_than[6]',
            'title' => 'required|max_length[255]',
            'review_text' => 'required|min_length[50]|max_length[2000]',
            'pros' => 'permit_empty|max_length[1000]',
            'cons' => 'permit_empty|max_length[1000]',
        ], [
            'rating' => [
                'required' => 'Rating is required.',
                'greater_than' => 'Rating must be between 1 and 5.',
                'less_than' => 'Rating must be between 1 and 5.',
            ],
            'title' => [
                'required' => 'Review title is required.',
                'max_length' => 'Title cannot exceed 255 characters.',
            ],
            'review_text' => [
                'required' => 'Review text is required.',
                'min_length' => 'Review must be at least 50 characters.',
                'max_length' => 'Review cannot exceed 2000 characters.',
            ],
        ]);
        
        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        // Prepare review data
        $reviewData = [
            'app_id' => $appId,
            'user_id' => $userId,
            'rating' => (int)$this->request->getPost('rating'),
            'title' => $this->request->getPost('title'),
            'review_text' => $this->request->getPost('review_text'),
            'pros' => $this->request->getPost('pros') ?? null,
            'cons' => $this->request->getPost('cons') ?? null,
            'approval_status' => 'pending',
        ];
        
        // Create review
        try {
            $reviewId = $this->reviewRepository->create($reviewData);
            
            if ($reviewId) {
                return redirect()->back()
                               ->with('success', 'Your review has been submitted and is pending approval. Thank you for your feedback!');
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Failed to submit review. Please try again.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Review submission error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'An error occurred while submitting your review. Please try again.');
        }
    }
    
    /**
     * Submit a scam report for an app
     * 
     * @param int $appId App ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function submitScamReport(int $appId)
    {
        // Check if user is authenticated
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login')
                           ->with('error', 'You must be logged in to submit a scam report.');
        }
        
        $userId = session()->get('user_id');
        
        // Check if app exists
        $app = $this->appRepository->find($appId);
        if (!$app) {
            return redirect()->back()
                           ->with('error', 'App not found.');
        }
        
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|max_length[255]',
            'description' => 'required|min_length[100]|max_length[3000]',
            'risk_level' => 'required|in_list[low,medium,high]',
            'evidence_url_1' => 'permit_empty|valid_url|max_length[500]',
            'evidence_url_2' => 'permit_empty|valid_url|max_length[500]',
            'evidence_url_3' => 'permit_empty|valid_url|max_length[500]',
            'evidence_url_4' => 'permit_empty|valid_url|max_length[500]',
            'evidence_url_5' => 'permit_empty|valid_url|max_length[500]',
        ], [
            'title' => [
                'required' => 'Report title is required.',
                'max_length' => 'Title cannot exceed 255 characters.',
            ],
            'description' => [
                'required' => 'Report description is required.',
                'min_length' => 'Description must be at least 100 characters.',
                'max_length' => 'Description cannot exceed 3000 characters.',
            ],
            'risk_level' => [
                'required' => 'Risk level is required.',
                'in_list' => 'Invalid risk level selected.',
            ],
        ]);
        
        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }
        
        // Collect evidence URLs (max 5)
        $evidenceUrls = [];
        for ($i = 1; $i <= 5; $i++) {
            $url = $this->request->getPost("evidence_url_{$i}");
            if (!empty($url)) {
                $evidenceUrls[] = $url;
            }
        }
        
        // Prepare scam report data
        $scamReportData = [
            'app_id' => $appId,
            'user_id' => $userId,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'risk_level' => $this->request->getPost('risk_level'),
            'evidence_urls' => !empty($evidenceUrls) ? json_encode($evidenceUrls) : null,
            'approval_status' => 'pending',
        ];
        
        // Create scam report
        try {
            $reportId = $this->scamReportRepository->create($scamReportData);
            
            if ($reportId) {
                return redirect()->back()
                               ->with('success', 'Your scam report has been submitted and is pending verification. Thank you for helping keep the community safe!');
            } else {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Failed to submit scam report. Please try again.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Scam report submission error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'An error occurred while submitting your scam report. Please try again.');
        }
    }
}
