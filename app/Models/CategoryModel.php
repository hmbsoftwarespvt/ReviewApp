<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CategoryModel
 * 
 * Manages app categories for classification and browsing.
 * 
 * Relationships:
 * - belongsToMany: apps (AppModel) via app_categories
 */
class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'icon',
        'display_order',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name'  => 'required|max_length[100]|is_unique[categories.name,id,{id}]',
        'slug'  => 'required|max_length[100]|alpha_dash|is_unique[categories.slug,id,{id}]',
        'icon'  => 'permit_empty|max_length[100]',
        'display_order' => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Category name is required',
            'max_length' => 'Category name cannot exceed 100 characters',
            'is_unique'  => 'Category name must be unique',
        ],
        'slug' => [
            'required'   => 'Slug is required',
            'is_unique'  => 'Slug must be unique',
            'alpha_dash' => 'Slug can only contain alphanumeric characters, dashes, and underscores',
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
     * Find category by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get all categories ordered by display order
     */
    public function getAllOrdered(): array
    {
        return $this->orderBy('display_order', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Get apps in category
     */
    public function getApps(int $categoryId, int $limit = 24, int $offset = 0): array
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
     * Get app count in category
     */
    public function getAppCount(int $categoryId): int
    {
        $db = \Config\Database::connect();
        
        return $db->table('app_categories')
                  ->join('apps', 'apps.id = app_categories.app_id')
                  ->where('app_categories.category_id', $categoryId)
                  ->where('apps.approval_status', 'approved')
                  ->countAllResults();
    }

    /**
     * Get categories with app counts
     */
    public function getAllWithAppCounts(): array
    {
        $categories = $this->getAllOrdered();
        
        foreach ($categories as &$category) {
            $category['app_count'] = $this->getAppCount($category['id']);
        }
        
        return $categories;
    }
}
