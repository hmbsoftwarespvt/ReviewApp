<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ScamReportModel
 * 
 * Manages scam reports for apps with verification workflow.
 * 
 * Relationships:
 * - belongsTo: app (AppModel)
 * - belongsTo: user (UserModel)
 */
class ScamReportModel extends Model
{
    protected $table            = 'scam_reports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'app_id',
        'user_id',
        'title',
        'description',
        'risk_level',
        'evidence_urls',
        'approval_status',
        'verification_notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'app_id'      => 'required|integer|is_not_unique[apps.id]',
        'user_id'     => 'required|integer|is_not_unique[users.id]',
        'title'       => 'required|max_length[255]',
        'description' => 'required|min_length[100]|max_length[3000]',
        'risk_level'  => 'required|in_list[low,medium,high]',
        'approval_status' => 'permit_empty|in_list[pending,approved,rejected]',
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Report title is required',
            'max_length' => 'Title cannot exceed 255 characters',
        ],
        'description' => [
            'required'   => 'Report description is required',
            'min_length' => 'Description must be at least 100 characters',
            'max_length' => 'Description cannot exceed 3000 characters',
        ],
        'risk_level' => [
            'required' => 'Risk level is required',
            'in_list'  => 'Risk level must be low, medium, or high',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['validateEvidenceUrls'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['validateEvidenceUrls'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Validate evidence URLs (max 5)
     */
    protected function validateEvidenceUrls(array $data): array
    {
        if (isset($data['data']['evidence_urls'])) {
            $urls = is_string($data['data']['evidence_urls']) 
                ? json_decode($data['data']['evidence_urls'], true) 
                : $data['data']['evidence_urls'];
            
            if (is_array($urls) && count($urls) > 5) {
                throw new \RuntimeException('Maximum 5 evidence URLs allowed');
            }
        }
        
        return $data;
    }

    /**
     * Get scam reports by app
     */
    public function getByApp(int $appId, string $status = 'approved', int $limit = 10, int $offset = 0): array
    {
        return $this->where('app_id', $appId)
                    ->where('approval_status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Get scam reports by user
     */
    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get pending scam reports
     */
    public function getPending(int $limit = 20, int $offset = 0): array
    {
        return $this->where('approval_status', 'pending')
                    ->orderBy('created_at', 'ASC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Get all scam reports with filters
     */
    public function getAll(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $builder = $this;
        
        if (!empty($filters['approval_status'])) {
            $builder = $builder->where('approval_status', $filters['approval_status']);
        }
        
        if (!empty($filters['risk_level'])) {
            $builder = $builder->where('risk_level', $filters['risk_level']);
        }
        
        if (!empty($filters['app_id'])) {
            $builder = $builder->where('app_id', $filters['app_id']);
        }
        
        return $builder->orderBy('created_at', 'DESC')
                      ->limit($limit, $offset)
                      ->findAll();
    }

    /**
     * Get count by app
     */
    public function getCountByApp(int $appId, string $status = 'approved'): int
    {
        return $this->where('app_id', $appId)
                    ->where('approval_status', $status)
                    ->countAllResults();
    }

    /**
     * Get count by risk level for app
     */
    public function getCountByRiskLevel(int $appId, string $riskLevel): int
    {
        return $this->where('app_id', $appId)
                    ->where('approval_status', 'approved')
                    ->where('risk_level', $riskLevel)
                    ->countAllResults();
    }

    /**
     * Update approval status
     */
    public function updateStatus(int $reportId, string $status, ?string $notes = null): bool
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }
        
        $data = ['approval_status' => $status];
        
        if ($notes !== null) {
            $data['verification_notes'] = $notes;
        }
        
        return $this->update($reportId, $data);
    }

    /**
     * Update risk level
     */
    public function updateRiskLevel(int $reportId, string $riskLevel): bool
    {
        if (!in_array($riskLevel, ['low', 'medium', 'high'])) {
            return false;
        }
        
        return $this->update($reportId, ['risk_level' => $riskLevel]);
    }

    /**
     * Get scam report with user and app details
     */
    public function getWithDetails(int $reportId): ?array
    {
        $db = \Config\Database::connect();
        
        return $db->table('scam_reports')
                  ->select('scam_reports.*, users.username, users.email, apps.name as app_name, apps.slug as app_slug')
                  ->join('users', 'users.id = scam_reports.user_id')
                  ->join('apps', 'apps.id = scam_reports.app_id')
                  ->where('scam_reports.id', $reportId)
                  ->get()
                  ->getRowArray();
    }

    /**
     * Get scam reports with user details for app
     */
    public function getByAppWithUser(int $appId, string $status = 'approved', int $limit = 10, int $offset = 0): array
    {
        $db = \Config\Database::connect();
        
        return $db->table('scam_reports')
                  ->select('scam_reports.*, users.username')
                  ->join('users', 'users.id = scam_reports.user_id')
                  ->where('scam_reports.app_id', $appId)
                  ->where('scam_reports.approval_status', $status)
                  ->orderBy('scam_reports.created_at', 'DESC')
                  ->limit($limit, $offset)
                  ->get()
                  ->getResultArray();
    }
}
