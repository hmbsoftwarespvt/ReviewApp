<?php

namespace App\Controllers;

use App\Models\AppModel;
use App\Services\TrustScoreService;

/**
 * Comparison Controller
 * 
 * Handles app comparison functionality allowing users to compare
 * 2-4 apps side-by-side.
 */
class Comparison extends BaseController
{
    protected AppModel $appModel;
    protected TrustScoreService $trustScoreService;
    
    public function __construct()
    {
        $this->appModel = new AppModel();
        $this->trustScoreService = new TrustScoreService();
    }
    
    /**
     * Display comparison tool
     * 
     * @return string
     */
    public function index(): string
    {
        // Get app IDs from session
        $selectedApps = session()->get('comparison_apps') ?? [];
        
        // Get app data for selected apps
        $apps = [];
        $appsWithBreakdown = [];
        
        foreach ($selectedApps as $appId) {
            $app = $this->appModel->find($appId);
            
            if ($app) {
                // Get trust score breakdown
                $breakdown = $this->trustScoreService->getTrustScoreBreakdown($appId);
                
                // Get average rating and review count
                $app['average_rating'] = $this->appModel->getAverageRating($appId);
                $app['review_count'] = $this->appModel->getReviewCount($appId);
                $app['scam_report_count'] = $this->appModel->getScamReportCount($appId);
                
                $apps[] = $app;
                $appsWithBreakdown[] = [
                    'app' => $app,
                    'breakdown' => $breakdown,
                ];
            }
        }
        
        // Find highest and lowest trust scores
        $highestScore = null;
        $lowestScore = null;
        
        if (!empty($apps)) {
            $scores = array_column($apps, 'trust_score');
            $highestScore = max($scores);
            $lowestScore = min($scores);
        }
        
        $data = [
            'title' => 'Compare Apps',
            'apps' => $appsWithBreakdown,
            'highestScore' => $highestScore,
            'lowestScore' => $lowestScore,
            'canAddMore' => count($apps) < 4,
            'canCompare' => count($apps) >= 2,
        ];
        
        return view('comparison/index', $data);
    }
    
    /**
     * Add app to comparison
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function add()
    {
        $appId = $this->request->getPost('app_id');
        
        if (!$appId) {
            return redirect()->back()->with('error', 'Please select an app to add.');
        }
        
        // Verify app exists
        $app = $this->appModel->find($appId);
        
        if (!$app) {
            return redirect()->back()->with('error', 'App not found.');
        }
        
        // Get current comparison apps
        $selectedApps = session()->get('comparison_apps') ?? [];
        
        // Check if already added
        if (in_array($appId, $selectedApps)) {
            return redirect()->back()->with('error', 'This app is already in your comparison.');
        }
        
        // Check limit (max 4 apps)
        if (count($selectedApps) >= 4) {
            return redirect()->back()->with('error', 'You can compare up to 4 apps at a time.');
        }
        
        // Add to session
        $selectedApps[] = $appId;
        session()->set('comparison_apps', $selectedApps);
        
        return redirect()->to('/comparison')->with('success', 'App added to comparison.');
    }
    
    /**
     * Remove app from comparison
     * 
     * @param int $appId
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function remove(int $appId)
    {
        // Get current comparison apps
        $selectedApps = session()->get('comparison_apps') ?? [];
        
        // Remove app
        $selectedApps = array_filter($selectedApps, function($id) use ($appId) {
            return $id != $appId;
        });
        
        // Reset array keys
        $selectedApps = array_values($selectedApps);
        
        // Update session
        session()->set('comparison_apps', $selectedApps);
        
        return redirect()->to('/comparison')->with('success', 'App removed from comparison.');
    }
    
    /**
     * Clear all apps from comparison
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function clear()
    {
        session()->remove('comparison_apps');
        
        return redirect()->to('/comparison')->with('success', 'Comparison cleared.');
    }
    
    /**
     * Search apps for comparison (AJAX)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function search()
    {
        $query = $this->request->getGet('q');
        
        if (empty($query) || strlen($query) < 2) {
            return $this->response->setJSON([]);
        }
        
        // Search apps
        $apps = $this->appModel
            ->like('name', $query)
            ->orLike('developer_name', $query)
            ->where('approval_status', 'approved')
            ->orderBy('trust_score', 'DESC')
            ->limit(10)
            ->findAll();
        
        // Format results
        $results = array_map(function($app) {
            return [
                'id' => $app['id'],
                'name' => $app['name'],
                'developer_name' => $app['developer_name'],
                'trust_score' => $app['trust_score'],
                'platform_type' => $app['platform_type'],
            ];
        }, $apps);
        
        return $this->response->setJSON($results);
    }
}
