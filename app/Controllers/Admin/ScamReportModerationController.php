<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ScamReportRepository;
use App\Services\TrustScoreService;

/**
 * ScamReportModerationController
 * 
 * Admin interface for verifying and moderating scam reports.
 * 
 * Features:
 * - View all pending scam reports
 * - Verify or reject scam reports
 * - Update risk level
 * - Add verification notes
 * - Trigger email notifications for high-risk approvals
 */
class ScamReportModerationController extends BaseController
{
    protected ScamReportRepository $scamReportRepository;
    protected TrustScoreService $trustScoreService;
    
    public function __construct()
    {
        $this->scamReportRepository = new ScamReportRepository();
        $this->trustScoreService = new TrustScoreService();
    }
    
    /**
     * Display scam report moderation list
     * 
     * @return string
     */
    public function index(): string
    {
        // Get filter parameters from query string
        $status = $this->request->getGet('status') ?? 'pending';
        $riskLevel = $this->request->getGet('risk_level') ?? null;
        $dateFrom = $this->request->getGet('date_from') ?? null;
        $dateTo = $this->request->getGet('date_to') ?? null;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;
        
        // Build filters array
        $filters = [];
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $filters['approval_status'] = $status;
        }
        if ($riskLevel && in_array($riskLevel, ['low', 'medium', 'high'])) {
            $filters['risk_level'] = $riskLevel;
        }
        
        // Get filtered scam reports
        $reports = $this->getFilteredReports($filters, $dateFrom, $dateTo, $page, $perPage);
        
        $data = [
            'title' => 'Scam Report Verification',
            'reports' => $reports['data'],
            'pagination' => $reports['pagination'],
            'filters' => [
                'status' => $status,
                'risk_level' => $riskLevel,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ];
        
        return view('admin/scam_reports/index', $data);
    }
    
    /**
     * Verify a scam report
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function verify(int $id)
    {
        $report = $this->scamReportRepository->getWithDetails($id);
        
        if (!$report) {
            return redirect()->back()->with('error', 'Scam report not found.');
        }
        
        // Get verification notes from POST data
        $notes = $this->request->getPost('verification_notes');
        
        // Update report status to approved
        $success = $this->scamReportRepository->updateStatus($id, 'approved', $notes);
        
        if ($success) {
            // Trigger trust score recalculation for the app
            $this->trustScoreService->invalidateCache($report['app_id']);
            $this->trustScoreService->calculateTrustScore($report['app_id']);
            
            // If high-risk, trigger email notification (placeholder for Task 33)
            if ($report['risk_level'] === 'high') {
                $this->sendHighRiskNotification($report);
            }
            
            return redirect()->back()->with('success', 'Scam report verified successfully. Trust score recalculated.');
        }
        
        return redirect()->back()->with('error', 'Failed to verify scam report.');
    }
    
    /**
     * Reject a scam report
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function reject(int $id)
    {
        $report = $this->scamReportRepository->find($id);
        
        if (!$report) {
            return redirect()->back()->with('error', 'Scam report not found.');
        }
        
        // Get verification notes from POST data
        $notes = $this->request->getPost('verification_notes');
        
        // Update report status to rejected
        $success = $this->scamReportRepository->updateStatus($id, 'rejected', $notes);
        
        if ($success) {
            return redirect()->back()->with('success', 'Scam report rejected successfully.');
        }
        
        return redirect()->back()->with('error', 'Failed to reject scam report.');
    }
    
    /**
     * Update risk level for a scam report
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function updateRisk(int $id)
    {
        $report = $this->scamReportRepository->find($id);
        
        if (!$report) {
            return redirect()->back()->with('error', 'Scam report not found.');
        }
        
        // Get new risk level from POST data
        $newRiskLevel = $this->request->getPost('risk_level');
        
        if (!in_array($newRiskLevel, ['low', 'medium', 'high'])) {
            return redirect()->back()->with('error', 'Invalid risk level.');
        }
        
        // Update risk level
        $success = $this->scamReportRepository->updateRiskLevel($id, $newRiskLevel);
        
        if ($success) {
            // If report is approved and risk level changed to high, trigger notification
            if ($report['approval_status'] === 'approved' && $newRiskLevel === 'high') {
                $updatedReport = $this->scamReportRepository->getWithDetails($id);
                $this->sendHighRiskNotification($updatedReport);
            }
            
            return redirect()->back()->with('success', 'Risk level updated successfully.');
        }
        
        return redirect()->back()->with('error', 'Failed to update risk level.');
    }
    
    /**
     * Get filtered scam reports based on criteria
     * 
     * @param array $filters
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @param int $page
     * @param int $perPage
     * @return array
     */
    protected function getFilteredReports(array $filters, ?string $dateFrom, ?string $dateTo, int $page, int $perPage): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('scam_reports')
                     ->select('scam_reports.*, users.username, users.email, apps.name as app_name, apps.slug as app_slug')
                     ->join('users', 'users.id = scam_reports.user_id')
                     ->join('apps', 'apps.id = scam_reports.app_id');
        
        // Apply status filter
        if (!empty($filters['approval_status'])) {
            $builder->where('scam_reports.approval_status', $filters['approval_status']);
        }
        
        // Apply risk level filter
        if (!empty($filters['risk_level'])) {
            $builder->where('scam_reports.risk_level', $filters['risk_level']);
        }
        
        // Apply date range filter
        if ($dateFrom) {
            $builder->where('DATE(scam_reports.created_at) >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('DATE(scam_reports.created_at) <=', $dateTo);
        }
        
        // Get total count for pagination
        $total = $builder->countAllResults(false);
        
        // Apply pagination
        $offset = ($page - 1) * $perPage;
        $builder->orderBy('scam_reports.created_at', 'ASC')
               ->limit($perPage, $offset);
        
        $reports = $builder->get()->getResultArray();
        
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
     * Send high-risk scam alert notification
     * 
     * Placeholder for Task 33 - Email notification service
     * 
     * @param array $report
     * @return void
     */
    protected function sendHighRiskNotification(array $report): void
    {
        // TODO: Implement in Task 33 - NotificationService
        // This will send email notifications to all newsletter subscribers
        // when a high-risk scam report is approved
        
        log_message('info', "High-risk scam report approved for app: {$report['app_name']} (ID: {$report['app_id']})");
        log_message('info', "Email notification will be sent when NotificationService is implemented (Task 33)");
    }
}

