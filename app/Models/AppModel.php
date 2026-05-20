<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AppModel
 * 
 * Manages application entries with trust scores and security data.
 * 
 * Relationships:
 * - hasMany: reviews (ReviewModel)
 * - hasMany: scam_reports (ScamReportModel)
 * - hasMany: screenshots (ScreenshotModel)
 * - belongsToMany: categories (CategoryModel) via app_categories
 * - hasMany: activity_logs (ActivityLogModel)
 */
class AppModel extends Model
{
    protected $table            = 'apps';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'version',
        'size',
        'platform_type',
        'price',
        'developer_name',
        'release_date',
        'download_url',
        'youtube_link',
        'trust_score',
        'security_score',
        'developer_reputation',
        'view_count',
        'trending_score',
        'approval_status',
        'permissions',
        'has_encryption',
        'third_party_sdk_count',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id'            => 'permit_empty|integer',
        'name'          => 'required|max_length[255]',
        'slug'          => 'required|max_length[255]|alpha_dash|is_unique[apps.slug,id,{id}]',
        'platform_type' => 'required|in_list[android,ios,web,desktop]',
        'developer_name' => 'required|max_length[255]',
        'price'         => 'permit_empty|decimal|greater_than_equal_to[0]',
        'download_url'  => 'permit_empty|valid_url|max_length[500]',
        'approval_status' => 'permit_empty|in_list[pending,approved,rejected]',
        'trust_score'   => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'security_score' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[25]',
        'developer_reputation' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[20]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'App name is required',
            'max_length' => 'App name cannot exceed 255 characters',
        ],
        'slug' => [
            'required'   => 'Slug is required',
            'is_unique'  => 'Slug must be unique',
            'alpha_dash' => 'Slug can only contain alphanumeric characters, dashes, and underscores',
        ],
        'platform_type' => [
            'required' => 'Platform type is required',
            'in_list'  => 'Platform type must be android, ios, web, or desktop',
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
     * Override update to ensure the primary key is present in data for validation placeholders.
     */
    public function update($id = null, $data = null): bool
    {
        if (is_array($data) && !isset($data[$this->primaryKey])) {
            $data[$this->primaryKey] = $id;
        }
        return parent::update($id, $data);
    }

    /**
     * Find app by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get app reviews
     */
    public function getReviews(int $appId, string $status = 'approved'): array
    {
        $reviewModel = new \App\Models\ReviewModel();
        return $reviewModel->where('app_id', $appId)
                          ->where('approval_status', $status)
                          ->orderBy('created_at', 'DESC')
                          ->findAll();
    }

    /**
     * Get app scam reports
     */
    public function getScamReports(int $appId, string $status = 'approved'): array
    {
        $scamReportModel = new \App\Models\ScamReportModel();
        return $scamReportModel->where('app_id', $appId)
                              ->where('approval_status', $status)
                              ->orderBy('created_at', 'DESC')
                              ->findAll();
    }

    /**
     * Get app screenshots
     */
    public function getScreenshots(int $appId): array
    {
        $screenshotModel = new \App\Models\ScreenshotModel();
        return $screenshotModel->where('app_id', $appId)
                              ->orderBy('display_order', 'ASC')
                              ->findAll();
    }

    /**
     * Get app categories
     */
    public function getCategories(int $appId): array
    {
        $db = \Config\Database::connect();
        
        return $db->table('app_categories')
                  ->select('categories.*')
                  ->join('categories', 'categories.id = app_categories.category_id')
                  ->where('app_categories.app_id', $appId)
                  ->get()
                  ->getResultArray();
    }

    /**
     * Attach categories to app
     */
    public function attachCategories(int $appId, array $categoryIds): bool
    {
        $db = \Config\Database::connect();
        
        foreach ($categoryIds as $categoryId) {
            $db->table('app_categories')->insert([
                'app_id'      => $appId,
                'category_id' => $categoryId,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }
        
        return true;
    }

    /**
     * Detach all categories from app
     */
    public function detachCategories(int $appId): bool
    {
        $db = \Config\Database::connect();
        return $db->table('app_categories')->where('app_id', $appId)->delete();
    }

    /**
     * Sync categories (detach all and attach new)
     */
    public function syncCategories(int $appId, array $categoryIds): bool
    {
        $this->detachCategories($appId);
        return $this->attachCategories($appId, $categoryIds);
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(int $appId): bool
    {
        return $this->set('view_count', 'view_count + 1', false)
                    ->where('id', $appId)
                    ->update();
    }

    /**
     * Get apps by category
     */
    public function getByCategory(int $categoryId, int $limit = 24, int $offset = 0): array
    {
        $db = \Config\Database::connect();
        
        return $db->table('apps')
                  ->select('apps.*')
                  ->join('app_categories', 'app_categories.app_id = apps.id')
                  ->where('app_categories.category_id', $categoryId)
                  ->where('apps.approval_status', 'approved')
                  ->orderBy('apps.trust_score', 'DESC')
                  ->limit($limit, $offset)
                  ->get()
                  ->getResultArray();
    }

    /**
     * Get apps by developer
     */
    public function getByDeveloper(string $developerName): array
    {
        return $this->where('developer_name', $developerName)
                    ->where('approval_status', 'approved')
                    ->orderBy('trust_score', 'DESC')
                    ->findAll();
    }

    /**
     * Get trending apps
     */
    public function getTrending(int $limit = 12): array
    {
        return $this->where('approval_status', 'approved')
                    ->orderBy('trending_score', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Search apps
     */
    public function search(string $query, array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $builder = $this->where('approval_status', 'approved');
        
        if (!empty($query)) {
            $builder->groupStart()
                    ->like('name', $query)
                    ->orLike('developer_name', $query)
                    ->orLike('description', $query)
                    ->groupEnd();
        }
        
        if (!empty($filters['platform_type'])) {
            $builder->where('platform_type', $filters['platform_type']);
        }
        
        if (!empty($filters['category_id'])) {
            $db = \Config\Database::connect();
            $appIds = $db->table('app_categories')
                        ->select('app_id')
                        ->where('category_id', $filters['category_id'])
                        ->get()
                        ->getResultArray();
            
            $appIds = array_column($appIds, 'app_id');
            
            if (!empty($appIds)) {
                $builder->whereIn('id', $appIds);
            }
        }
        
        return $builder->orderBy('trust_score', 'DESC')
                      ->limit($limit, $offset)
                      ->findAll();
    }
    
    /**
     * Get average rating for app
     */
    public function getAverageRating(int $appId): float
    {
        $reviewModel = new \App\Models\ReviewModel();
        return $reviewModel->getAverageRating($appId);
    }
    
    /**
     * Get review count for app
     */
    public function getReviewCount(int $appId, string $status = 'approved'): int
    {
        $reviewModel = new \App\Models\ReviewModel();
        return $reviewModel->getReviewCount($appId, $status);
    }
    
    /**
     * Get scam report count for app
     */
    public function getScamReportCount(int $appId, string $status = 'approved'): int
    {
        $scamReportModel = new \App\Models\ScamReportModel();
        return $scamReportModel->getCountByApp($appId, $status);
    }
}
