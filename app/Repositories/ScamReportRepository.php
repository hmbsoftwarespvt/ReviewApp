<?php

namespace App\Repositories;

use App\Models\ScamReportModel;

/**
 * ScamReportRepository
 * 
 * Data access abstraction layer for scam reports.
 * Provides consistent interface for scam report-related database operations.
 */
class ScamReportRepository
{
    protected ScamReportModel $scamReportModel;
    
    public function __construct()
    {
        $this->scamReportModel = new ScamReportModel();
    }
    
    /**
     * Find scam report by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        return $this->scamReportModel->find($id);
    }
    
    /**
     * Get scam reports by app
     * 
     * @param int $appId
     * @param string $status
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByApp(int $appId, string $status = 'approved', int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        $reports = $this->scamReportModel->getByAppWithUser($appId, $status, $perPage, $offset);
        
        $total = $this->scamReportModel->where('app_id', $appId)
                                      ->where('approval_status', $status)
                                      ->countAllResults(false);
        
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
     * Get scam reports by user
     * 
     * @param int $userId
     * @return array
     */
    public function getByUser(int $userId): array
    {
        return $this->scamReportModel->getByUser($userId);
    }
    
    /**
     * Get pending scam reports
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPending(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $reports = $this->scamReportModel->getPending($perPage, $offset);
        
        // Enrich with user and app details
        $db = \Config\Database::connect();
        $enrichedReports = [];
        
        foreach ($reports as $report) {
            $details = $db->table('scam_reports')
                         ->select('scam_reports.*, users.username, users.email, apps.name as app_name, apps.slug as app_slug')
                         ->join('users', 'users.id = scam_reports.user_id')
                         ->join('apps', 'apps.id = scam_reports.app_id')
                         ->where('scam_reports.id', $report['id'])
                         ->get()
                         ->getRowArray();
            
            $enrichedReports[] = $details;
        }
        
        $total = $this->scamReportModel->where('approval_status', 'pending')
                                      ->countAllResults(false);
        
        return [
            'data' => $enrichedReports,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Get all scam reports with filters
     * 
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $reports = $this->scamReportModel->getAll($filters, $perPage, $offset);
        
        // Count total with same filters
        $builder = $this->scamReportModel;
        
        if (!empty($filters['approval_status'])) {
            $builder = $builder->where('approval_status', $filters['approval_status']);
        }
        
        if (!empty($filters['risk_level'])) {
            $builder = $builder->where('risk_level', $filters['risk_level']);
        }
        
        if (!empty($filters['app_id'])) {
            $builder = $builder->where('app_id', $filters['app_id']);
        }
        
        $total = $builder->countAllResults(false);
        
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
     * Create new scam report
     * 
     * @param array $data
     * @return int Scam report ID
     */
    public function create(array $data): int
    {
        return $this->scamReportModel->insert($data);
    }
    
    /**
     * Update scam report status
     * 
     * @param int $id
     * @param string $status
     * @param string|null $notes
     * @return bool
     */
    public function updateStatus(int $id, string $status, ?string $notes = null): bool
    {
        return $this->scamReportModel->updateStatus($id, $status, $notes);
    }
    
    /**
     * Update risk level
     * 
     * @param int $id
     * @param string $riskLevel
     * @return bool
     */
    public function updateRiskLevel(int $id, string $riskLevel): bool
    {
        return $this->scamReportModel->updateRiskLevel($id, $riskLevel);
    }
    
    /**
     * Delete scam report
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->scamReportModel->delete($id);
    }
    
    /**
     * Get count by app
     * 
     * @param int $appId
     * @param string $status
     * @return int
     */
    public function getCountByApp(int $appId, string $status = 'approved'): int
    {
        return $this->scamReportModel->getCountByApp($appId, $status);
    }
    
    /**
     * Get count by risk level for app
     * 
     * @param int $appId
     * @param string $riskLevel
     * @return int
     */
    public function getCountByRiskLevel(int $appId, string $riskLevel): int
    {
        return $this->scamReportModel->getCountByRiskLevel($appId, $riskLevel);
    }
    
    /**
     * Get scam report with full details
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithDetails(int $id): ?array
    {
        return $this->scamReportModel->getWithDetails($id);
    }
    
    /**
     * Get total scam report count
     * 
     * @param string|null $status
     * @return int
     */
    public function count(?string $status = null): int
    {
        $builder = $this->scamReportModel;
        
        if ($status !== null) {
            $builder = $builder->where('approval_status', $status);
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get scam reports by risk level
     * 
     * @param string $riskLevel
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByRiskLevel(string $riskLevel, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $reports = $this->scamReportModel->where('risk_level', $riskLevel)
                                        ->where('approval_status', 'approved')
                                        ->orderBy('created_at', 'DESC')
                                        ->limit($perPage, $offset)
                                        ->findAll();
        
        $total = $this->scamReportModel->where('risk_level', $riskLevel)
                                      ->where('approval_status', 'approved')
                                      ->countAllResults(false);
        
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
     * Get recent scam reports
     * 
     * @param int $limit
     * @param int $days
     * @return array
     */
    public function getRecent(int $limit = 10, int $days = 7): array
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->scamReportModel->where('approval_status', 'approved')
                                    ->where('created_at >=', $date)
                                    ->orderBy('created_at', 'DESC')
                                    ->limit($limit)
                                    ->findAll();
    }
    
    /**
     * Get high-risk scam reports
     * 
     * @param int $limit
     * @return array
     */
    public function getHighRisk(int $limit = 10): array
    {
        return $this->scamReportModel->where('risk_level', 'high')
                                    ->where('approval_status', 'approved')
                                    ->orderBy('created_at', 'DESC')
                                    ->limit($limit)
                                    ->findAll();
    }
    
    /**
     * Get scam report statistics by risk level
     * 
     * @return array
     */
    public function getStatsByRiskLevel(): array
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                risk_level,
                COUNT(*) as count
            FROM scam_reports
            WHERE approval_status = 'approved'
            GROUP BY risk_level
        ");
        
        $results = $query->getResultArray();
        
        $stats = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
        ];
        
        foreach ($results as $row) {
            $stats[$row['risk_level']] = (int) $row['count'];
        }
        
        return $stats;
    }
}
