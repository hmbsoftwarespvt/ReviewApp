<?php

namespace App\Repositories;

use App\Models\AppModel;
use App\Models\CategoryModel;

/**
 * AppRepository
 * 
 * Data access abstraction layer for apps.
 * Provides consistent interface for app-related database operations.
 */
class AppRepository
{
    protected AppModel $appModel;
    protected CategoryModel $categoryModel;
    
    public function __construct()
    {
        $this->appModel = new AppModel();
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * Find app by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        return $this->appModel->find($id);
    }
    
    /**
     * Find app by slug
     * 
     * @param string $slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->appModel->findBySlug($slug);
    }
    
    /**
     * Get all apps with filters and pagination
     * 
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 24): array
    {
        $builder = $this->appModel;
        
        // Apply filters
        if (!empty($filters['approval_status'])) {
            $builder = $builder->where('approval_status', $filters['approval_status']);
        } else {
            $builder = $builder->where('approval_status', 'approved');
        }
        
        if (!empty($filters['platform_type'])) {
            $builder = $builder->where('platform_type', $filters['platform_type']);
        }
        
        if (!empty($filters['developer_name'])) {
            $builder = $builder->where('developer_name', $filters['developer_name']);
        }
        
        if (!empty($filters['min_trust_score'])) {
            $builder = $builder->where('trust_score >=', $filters['min_trust_score']);
        }
        
        // Sorting
        $sortBy = $filters['sort_by'] ?? 'trust_score';
        $sortOrder = $filters['sort_order'] ?? 'DESC';
        
        $offset = ($page - 1) * $perPage;
        
        $apps = $builder->orderBy($sortBy, $sortOrder)
                       ->limit($perPage, $offset)
                       ->findAll();
        
        $total = $builder->countAllResults(false);
        
        return [
            'data' => $apps,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Create new app
     * 
     * @param array $data
     * @return int App ID
     */
    public function create(array $data): int
    {
        // Extract categories if present
        $categories = $data['categories'] ?? [];
        unset($data['categories']);
        
        // Create app
        $appId = $this->appModel->insert($data);
        
        // Attach categories
        if (!empty($categories) && $appId) {
            $this->appModel->attachCategories($appId, $categories);
        }
        
        return $appId;
    }
    
    /**
     * Update app
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        // Extract categories if present
        $categories = $data['categories'] ?? null;
        unset($data['categories']);
        
        // Update app
        $result = $this->appModel->update($id, $data);
        
        // Sync categories if provided
        if ($categories !== null && $result) {
            $this->appModel->syncCategories($id, $categories);
        }
        
        return $result;
    }
    
    /**
     * Delete app
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->appModel->delete($id);
    }
    
    /**
     * Increment view count
     * 
     * @param int $id
     * @return bool
     */
    public function incrementViewCount(int $id): bool
    {
        return $this->appModel->incrementViewCount($id);
    }
    
    /**
     * Get apps by category
     * 
     * @param int $categoryId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByCategory(int $categoryId, int $page = 1, int $perPage = 24): array
    {
        $offset = ($page - 1) * $perPage;
        
        $apps = $this->appModel->getByCategory($categoryId, $perPage, $offset);
        
        // Count total
        $db = \Config\Database::connect();
        $total = $db->table('app_categories')
                   ->where('category_id', $categoryId)
                   ->join('apps', 'apps.id = app_categories.app_id')
                   ->where('apps.approval_status', 'approved')
                   ->countAllResults();
        
        return [
            'data' => $apps,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Get apps by developer
     * 
     * @param string $developerName
     * @return array
     */
    public function getByDeveloper(string $developerName): array
    {
        return $this->appModel->getByDeveloper($developerName);
    }
    
    /**
     * Get trending apps
     * 
     * @param int $limit
     * @return array
     */
    public function getTrending(int $limit = 12): array
    {
        return $this->appModel->getTrending($limit);
    }
    
    /**
     * Search apps
     * 
     * @param string $query
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function search(string $query, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $apps = $this->appModel->search($query, $filters, $perPage, $offset);
        
        // Count total results
        $builder = $this->appModel->where('approval_status', 'approved');
        
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
        
        $total = $builder->countAllResults(false);
        
        return [
            'data' => $apps,
            'query' => $query,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Get app with full details (categories, reviews, scam reports, screenshots)
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithDetails(int $id): ?array
    {
        $app = $this->find($id);
        
        if (!$app) {
            return null;
        }
        
        // Add related data
        $app['categories'] = $this->appModel->getCategories($id);
        $app['reviews'] = $this->appModel->getReviews($id, 'approved');
        $app['scam_reports'] = $this->appModel->getScamReports($id, 'approved');
        $app['screenshots'] = $this->appModel->getScreenshots($id);
        
        return $app;
    }
    
    /**
     * Get apps with pending approval
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPending(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $apps = $this->appModel->where('approval_status', 'pending')
                              ->orderBy('created_at', 'ASC')
                              ->limit($perPage, $offset)
                              ->findAll();
        
        $total = $this->appModel->where('approval_status', 'pending')
                               ->countAllResults(false);
        
        return [
            'data' => $apps,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Get top apps by trust score
     * 
     * @param int $limit
     * @return array
     */
    public function getTopByTrustScore(int $limit = 10): array
    {
        return $this->appModel->where('approval_status', 'approved')
                             ->orderBy('trust_score', 'DESC')
                             ->limit($limit)
                             ->findAll();
    }
    
    /**
     * Get top apps by view count
     * 
     * @param int $limit
     * @return array
     */
    public function getTopByViews(int $limit = 10): array
    {
        return $this->appModel->where('approval_status', 'approved')
                             ->orderBy('view_count', 'DESC')
                             ->limit($limit)
                             ->findAll();
    }
    
    /**
     * Get total app count
     * 
     * @param string|null $status
     * @return int
     */
    public function count(?string $status = null): int
    {
        $builder = $this->appModel;
        
        if ($status !== null) {
            $builder = $builder->where('approval_status', $status);
        }
        
        return $builder->countAllResults();
    }
}
