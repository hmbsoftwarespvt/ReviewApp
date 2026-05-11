<?php

namespace App\Controllers;

use App\Services\SearchService;
use App\Models\CategoryModel;

/**
 * SearchController
 * 
 * Handles app search functionality with filtering and sorting.
 */
class SearchController extends BaseController
{
    protected SearchService $searchService;
    protected CategoryModel $categoryModel;
    
    public function __construct()
    {
        $this->searchService = new SearchService();
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * Display search results
     * 
     * GET /search
     * 
     * Query parameters:
     * - q: Search query
     * - category: Category ID filter
     * - platform: Platform type filter (android, ios, web, desktop)
     * - price_type: Price filter (free, paid)
     * - price_min: Minimum price
     * - price_max: Maximum price
     * - sort: Sort by (relevance, trust_score, date, name)
     * - order: Sort order (asc, desc)
     * - page: Page number
     */
    public function index()
    {
        // Get search parameters
        $query = $this->request->getGet('q') ?? '';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $sortBy = $this->request->getGet('sort') ?? 'relevance';
        $sortOrder = strtoupper($this->request->getGet('order') ?? 'DESC');
        
        // Validate page number
        if ($page < 1) {
            $page = 1;
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['ASC', 'DESC'])) {
            $sortOrder = 'DESC';
        }
        
        // Build filters array
        $filters = [];
        
        if ($categoryId = $this->request->getGet('category')) {
            $filters['category_id'] = (int) $categoryId;
        }
        
        if ($platform = $this->request->getGet('platform')) {
            if (in_array($platform, ['android', 'ios', 'web', 'desktop'])) {
                $filters['platform_type'] = $platform;
            }
        }
        
        if ($priceType = $this->request->getGet('price_type')) {
            if (in_array($priceType, ['free', 'paid'])) {
                $filters['price_type'] = $priceType;
            }
        }
        
        if ($priceMin = $this->request->getGet('price_min')) {
            $filters['price_min'] = (float) $priceMin;
        }
        
        if ($priceMax = $this->request->getGet('price_max')) {
            $filters['price_max'] = (float) $priceMax;
        }
        
        // Perform search
        $results = $this->searchService->search(
            $query,
            $filters,
            $sortBy,
            $sortOrder,
            $page,
            20 // 20 results per page as per requirements
        );
        
        // Get all categories for filter dropdown
        $categories = $this->categoryModel->orderBy('display_order', 'ASC')->findAll();
        
        // Get suggestions if no results
        $suggestions = [];
        if (empty($results['data']) && !empty($query)) {
            $suggestions = $this->searchService->getSuggestions($query);
        }
        
        // Prepare view data
        $data = [
            'title' => !empty($query) ? "Search Results for '{$query}'" : 'Search Apps',
            'query' => $query,
            'results' => $results['data'],
            'pagination' => $results['pagination'],
            'filters' => $filters,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'categories' => $categories,
            'suggestions' => $suggestions,
            'active_filters' => $this->getActiveFiltersCount($filters),
        ];
        
        return view('search_results', $data);
    }
    
    /**
     * Count active filters
     * 
     * @param array $filters
     * @return int
     */
    protected function getActiveFiltersCount(array $filters): int
    {
        $count = 0;
        
        if (!empty($filters['category_id'])) {
            $count++;
        }
        
        if (!empty($filters['platform_type'])) {
            $count++;
        }
        
        if (!empty($filters['price_type'])) {
            $count++;
        }
        
        if (isset($filters['price_min']) || isset($filters['price_max'])) {
            $count++;
        }
        
        return $count;
    }
    
    /**
     * AJAX endpoint for live search suggestions
     * 
     * GET /search/suggest
     */
    public function suggest()
    {
        $query = $this->request->getGet('q') ?? '';
        
        if (strlen($query) < 2) {
            return $this->response->setJSON([]);
        }
        
        // Quick search for app names and developers
        $db = \Config\Database::connect();
        
        $apps = $db->table('apps')
                  ->select('name, developer_name, slug')
                  ->where('approval_status', 'approved')
                  ->groupStart()
                      ->like('name', $query)
                      ->orLike('developer_name', $query)
                  ->groupEnd()
                  ->orderBy('trust_score', 'DESC')
                  ->limit(10)
                  ->get()
                  ->getResultArray();
        
        $suggestions = [];
        foreach ($apps as $app) {
            $suggestions[] = [
                'name' => $app['name'],
                'developer' => $app['developer_name'],
                'url' => base_url("app/{$app['slug']}"),
            ];
        }
        
        return $this->response->setJSON($suggestions);
    }
}
