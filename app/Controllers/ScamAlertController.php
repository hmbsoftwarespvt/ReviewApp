<?php

namespace App\Controllers;

use App\Repositories\ScamReportRepository;
use App\Models\CategoryModel;
use App\Models\AppModel;
use App\Repositories\AppRepository;

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
     * Display scam report form
     * 
     * @return string
     */
    public function reportForm()
    {
        $data = [
            'title' => 'Report a Scam',
            'app_name' => $this->request->getGet('app') ?? '',
        ];

        return view('scam_report_form', $data);
    }

    /**
     * Handle scam report submission
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function submitReport()
    {
        $userId = session()->get('user_id');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'app_name' => 'required|max_length[255]',
            'title' => 'required|max_length[255]',
            'description' => 'required|min_length[100]|max_length[3000]',
            'risk_level' => 'required|in_list[low,medium,high]',
            'evidence_url_1' => 'permit_empty|valid_url|max_length[500]',
            'evidence_url_2' => 'permit_empty|valid_url|max_length[500]',
            'evidence_url_3' => 'permit_empty|valid_url|max_length[500]',
            'evidence_url_4' => 'permit_empty|valid_url|max_length[500]',
            'evidence_url_5' => 'permit_empty|valid_url|max_length[500]',
        ], [
            'app_name' => ['required' => 'App name is required.'],
            'title' => ['required' => 'Report title is required.', 'max_length' => 'Title cannot exceed 255 characters.'],
            'description' => ['required' => 'Description is required.', 'min_length' => 'Description must be at least 100 characters.', 'max_length' => 'Description cannot exceed 3000 characters.'],
            'risk_level' => ['required' => 'Risk level is required.', 'in_list' => 'Invalid risk level selected.'],
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $appName = $this->request->getPost('app_name');

        $appModel = new AppModel();
        $app = $appModel->like('name', $appName)->first();

        if (!$app) {
            return redirect()->back()->withInput()->with('error', 'App not found. Please enter a valid app name.');
        }

        $evidenceUrls = [];
        for ($i = 1; $i <= 5; $i++) {
            $url = $this->request->getPost("evidence_url_{$i}");
            if (!empty($url)) {
                $evidenceUrls[] = $url;
            }
        }

        $scamReportData = [
            'app_id' => $app['id'],
            'user_id' => $userId,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'risk_level' => $this->request->getPost('risk_level'),
            'evidence_urls' => !empty($evidenceUrls) ? json_encode($evidenceUrls) : null,
            'approval_status' => 'pending',
        ];

        try {
            $reportId = $this->scamReportRepository->create($scamReportData);

            if ($reportId) {
                return redirect()->to('/scam-alerts')->with('success', 'Your scam report has been submitted and is pending verification.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to submit scam report. Please try again.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Scam report submission error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Display scam alert detail page for an app by slug
     * 
     * @param string $slug App slug
     * @return string
     */
    public function show(string $slug)
    {
        $appModel = new AppModel();
        $app = $appModel->where('slug', $slug)->first();

        if (!$app) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "App not found: {$slug}"
            );
        }

        $reports = $this->scamReportRepository->getByApp($app['id'], 'approved');
        $riskCounts = [
            'high' => $this->scamReportRepository->getCountByRiskLevel($app['id'], 'high'),
            'medium' => $this->scamReportRepository->getCountByRiskLevel($app['id'], 'medium'),
            'low' => $this->scamReportRepository->getCountByRiskLevel($app['id'], 'low'),
        ];

        $data = [
            'title' => 'Scam Alert: ' . $app['name'],
            'app' => $app,
            'reports' => $reports['data'],
            'pagination' => $reports['pagination'],
            'risk_counts' => $riskCounts,
            'total_reports' => array_sum($riskCounts),
        ];

        return view('scam_alert_detail', $data);
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
