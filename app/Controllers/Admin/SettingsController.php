<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Services\TrustScoreService;

/**
 * SettingsController
 * 
 * Admin settings configuration interface.
 * 
 * Features:
 * - Trust algorithm component weights configuration
 * - Email notification settings (sender name and address)
 * - Pagination limits configuration
 * - Settings validation
 * - Changes apply within 60 seconds (via cache invalidation)
 */
class SettingsController extends BaseController
{
    protected SettingModel $settingModel;
    protected TrustScoreService $trustScoreService;
    
    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->trustScoreService = new TrustScoreService();
    }
    
    /**
     * Display settings configuration page
     * 
     * @return string
     */
    public function index(): string
    {
        // Load current settings
        $trustAlgorithmWeights = $this->getTrustAlgorithmWeights();
        $emailSettings = $this->getEmailSettings();
        $paginationSettings = $this->getPaginationSettings();
        
        $data = [
            'title' => 'Platform Settings',
            'trustAlgorithmWeights' => $trustAlgorithmWeights,
            'emailSettings' => $emailSettings,
            'paginationSettings' => $paginationSettings,
            'validation' => \Config\Services::validation(),
        ];
        
        return view('admin/settings/index', $data);
    }
    
    /**
     * Update settings
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to(base_url('admin/settings'));
        }
        
        $post = $this->request->getPost();
        $settingType = $post['setting_type'] ?? '';
        
        // Validate and save based on setting type
        switch ($settingType) {
            case 'trust_algorithm':
                return $this->updateTrustAlgorithmWeights($post);
                
            case 'email':
                return $this->updateEmailSettings($post);
                
            case 'pagination':
                return $this->updatePaginationSettings($post);
                
            default:
                return redirect()->to(base_url('admin/settings'))
                    ->with('error', 'Invalid setting type');
        }
    }
    
    // ========== Protected Helper Methods ==========
    
    /**
     * Get trust algorithm weights
     */
    protected function getTrustAlgorithmWeights(): array
    {
        return [
            'review_rating' => $this->settingModel->get('trust_algorithm_review_rating', 30),
            'security_score' => $this->settingModel->get('trust_algorithm_security_score', 25),
            'developer_reputation' => $this->settingModel->get('trust_algorithm_developer_reputation', 20),
            'scam_report_count' => $this->settingModel->get('trust_algorithm_scam_report_count', 15),
            'app_age' => $this->settingModel->get('trust_algorithm_app_age', 10),
        ];
    }
    
    /**
     * Get email settings
     */
    protected function getEmailSettings(): array
    {
        return [
            'sender_name' => $this->settingModel->get('email_sender_name', 'AppTrust Platform'),
            'sender_email' => $this->settingModel->get('email_sender_email', 'noreply@apptrust.com'),
        ];
    }
    
    /**
     * Get pagination settings
     */
    protected function getPaginationSettings(): array
    {
        return [
            'search_results' => $this->settingModel->get('pagination_search_results', 20),
            'category_pages' => $this->settingModel->get('pagination_category_pages', 24),
            'blog_listings' => $this->settingModel->get('pagination_blog_listings', 12),
            'reviews_per_page' => $this->settingModel->get('pagination_reviews_per_page', 10),
            'scam_reports_per_page' => $this->settingModel->get('pagination_scam_reports_per_page', 20),
        ];
    }
    
    /**
     * Update trust algorithm weights
     */
    protected function updateTrustAlgorithmWeights(array $post): \CodeIgniter\HTTP\RedirectResponse
    {
        // Validation rules
        $rules = [
            'review_rating' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'security_score' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'developer_reputation' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'scam_report_count' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'app_age' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validation failed. Please check your input.')
                ->with('validation', $this->validator);
        }
        
        // Validate that weights sum to 100
        $sum = (float)$post['review_rating'] 
             + (float)$post['security_score'] 
             + (float)$post['developer_reputation'] 
             + (float)$post['scam_report_count'] 
             + (float)$post['app_age'];
        
        if (abs($sum - 100) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Trust algorithm weights must sum to 100. Current sum: {$sum}");
        }
        
        // Save settings
        $this->settingModel->setSetting('trust_algorithm_review_rating', $post['review_rating'], 'integer', 'Weight for review rating component');
        $this->settingModel->setSetting('trust_algorithm_security_score', $post['security_score'], 'integer', 'Weight for security score component');
        $this->settingModel->setSetting('trust_algorithm_developer_reputation', $post['developer_reputation'], 'integer', 'Weight for developer reputation component');
        $this->settingModel->setSetting('trust_algorithm_scam_report_count', $post['scam_report_count'], 'integer', 'Weight for scam report count component');
        $this->settingModel->setSetting('trust_algorithm_app_age', $post['app_age'], 'integer', 'Weight for app age component');
        
        // Clear cache to apply changes immediately
        $cache = \Config\Services::cache();
        $cache->clean();
        
        return redirect()->to(base_url('admin/settings'))
            ->with('success', 'Trust algorithm weights updated successfully. Changes will apply within 60 seconds.');
    }
    
    /**
     * Update email settings
     */
    protected function updateEmailSettings(array $post): \CodeIgniter\HTTP\RedirectResponse
    {
        // Validation rules
        $rules = [
            'sender_name' => 'required|max_length[255]',
            'sender_email' => 'required|valid_email|max_length[255]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validation failed. Please check your input.')
                ->with('validation', $this->validator);
        }
        
        // Save settings
        $this->settingModel->setSetting('email_sender_name', $post['sender_name'], 'string', 'Email sender name for notifications');
        $this->settingModel->setSetting('email_sender_email', $post['sender_email'], 'string', 'Email sender address for notifications');
        
        return redirect()->to(base_url('admin/settings'))
            ->with('success', 'Email settings updated successfully.');
    }
    
    /**
     * Update pagination settings
     */
    protected function updatePaginationSettings(array $post): \CodeIgniter\HTTP\RedirectResponse
    {
        // Validation rules
        $rules = [
            'search_results' => 'required|integer|greater_than[0]|less_than_equal_to[100]',
            'category_pages' => 'required|integer|greater_than[0]|less_than_equal_to[100]',
            'blog_listings' => 'required|integer|greater_than[0]|less_than_equal_to[100]',
            'reviews_per_page' => 'required|integer|greater_than[0]|less_than_equal_to[100]',
            'scam_reports_per_page' => 'required|integer|greater_than[0]|less_than_equal_to[100]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validation failed. Please check your input.')
                ->with('validation', $this->validator);
        }
        
        // Save settings
        $this->settingModel->setSetting('pagination_search_results', $post['search_results'], 'integer', 'Items per page for search results');
        $this->settingModel->setSetting('pagination_category_pages', $post['category_pages'], 'integer', 'Items per page for category pages');
        $this->settingModel->setSetting('pagination_blog_listings', $post['blog_listings'], 'integer', 'Items per page for blog listings');
        $this->settingModel->setSetting('pagination_reviews_per_page', $post['reviews_per_page'], 'integer', 'Reviews per page');
        $this->settingModel->setSetting('pagination_scam_reports_per_page', $post['scam_reports_per_page'], 'integer', 'Scam reports per page');
        
        return redirect()->to(base_url('admin/settings'))
            ->with('success', 'Pagination settings updated successfully.');
    }
}
