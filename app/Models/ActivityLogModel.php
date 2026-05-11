<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ActivityLogModel
 * 
 * Manages 24-hour activity metrics for trending calculation.
 * 
 * Relationships:
 * - belongsTo: app (AppModel)
 */
class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'app_id',
        'activity_type',
        'activity_date',
        'count',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'app_id'        => 'required|integer|is_not_unique[apps.id]',
        'activity_type' => 'required|in_list[view,review,scam_report]',
        'activity_date' => 'required|valid_date',
        'count'         => 'permit_empty|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'activity_type' => [
            'required' => 'Activity type is required',
            'in_list'  => 'Activity type must be view, review, or scam_report',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Log activity (increment count if exists, insert if not)
     */
    public function logActivity(int $appId, string $activityType, ?string $date = null): bool
    {
        if (!in_array($activityType, ['view', 'review', 'scam_report'])) {
            return false;
        }
        
        $date = $date ?? date('Y-m-d');
        
        $existing = $this->where('app_id', $appId)
                        ->where('activity_type', $activityType)
                        ->where('activity_date', $date)
                        ->first();
        
        if ($existing) {
            return $this->set('count', 'count + 1', false)
                        ->where('id', $existing['id'])
                        ->update();
        }
        
        return $this->insert([
            'app_id'        => $appId,
            'activity_type' => $activityType,
            'activity_date' => $date,
            'count'         => 1,
        ]) !== false;
    }

    /**
     * Get activity count for app on specific date
     */
    public function getActivityCount(int $appId, string $activityType, string $date): int
    {
        $result = $this->where('app_id', $appId)
                      ->where('activity_type', $activityType)
                      ->where('activity_date', $date)
                      ->first();
        
        return $result ? (int) $result['count'] : 0;
    }

    /**
     * Get 24-hour activity metrics for app
     */
    public function get24HourMetrics(int $appId): array
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        $activities = $this->where('app_id', $appId)
                          ->where('activity_date', $yesterday)
                          ->findAll();
        
        $metrics = [
            'views'        => 0,
            'reviews'      => 0,
            'scam_reports' => 0,
        ];
        
        foreach ($activities as $activity) {
            switch ($activity['activity_type']) {
                case 'view':
                    $metrics['views'] = (int) $activity['count'];
                    break;
                case 'review':
                    $metrics['reviews'] = (int) $activity['count'];
                    break;
                case 'scam_report':
                    $metrics['scam_reports'] = (int) $activity['count'];
                    break;
            }
        }
        
        return $metrics;
    }

    /**
     * Get activity metrics for date range
     */
    public function getMetricsForDateRange(int $appId, string $startDate, string $endDate): array
    {
        $activities = $this->where('app_id', $appId)
                          ->where('activity_date >=', $startDate)
                          ->where('activity_date <=', $endDate)
                          ->orderBy('activity_date', 'ASC')
                          ->findAll();
        
        $metrics = [];
        
        foreach ($activities as $activity) {
            $date = $activity['activity_date'];
            
            if (!isset($metrics[$date])) {
                $metrics[$date] = [
                    'views'        => 0,
                    'reviews'      => 0,
                    'scam_reports' => 0,
                ];
            }
            
            switch ($activity['activity_type']) {
                case 'view':
                    $metrics[$date]['views'] = (int) $activity['count'];
                    break;
                case 'review':
                    $metrics[$date]['reviews'] = (int) $activity['count'];
                    break;
                case 'scam_report':
                    $metrics[$date]['scam_reports'] = (int) $activity['count'];
                    break;
            }
        }
        
        return $metrics;
    }

    /**
     * Clean old activity logs (older than 30 days)
     */
    public function cleanOldLogs(int $daysToKeep = 30): bool
    {
        $cutoffDate = date('Y-m-d', strtotime("-{$daysToKeep} days"));
        return $this->where('activity_date <', $cutoffDate)->delete();
    }

    /**
     * Get total activity count by type for date range
     */
    public function getTotalActivityByType(string $activityType, string $startDate, string $endDate): int
    {
        $result = $this->selectSum('count', 'total')
                      ->where('activity_type', $activityType)
                      ->where('activity_date >=', $startDate)
                      ->where('activity_date <=', $endDate)
                      ->first();
        
        return $result ? (int) $result['total'] : 0;
    }
}
