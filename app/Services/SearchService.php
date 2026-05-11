<?php

namespace App\Services;

use App\Repositories\AppRepository;

/**
 * SearchService
 * 
 * Provides full-text search functionality with relevance ranking,
 * filtering, sorting, and search term highlighting.
 */
class SearchService
{
    protected AppRepository $appRepository;
    
    public function __construct()
    {
        $this->appRepository = new AppRepository();
    }
    
    /**
     * Search apps with filters and sorting
     * 
     * @param string $query Search query
     * @param array $filters Filters (category, platform, price)
     * @param string $sortBy Sort field (relevance, trust_score, created_at)
     * @param string $sortOrder Sort order (ASC, DESC)
     * @param int $page Page number
     * @param int $perPage Results per page
     * @return array Search results with pagination
     */
    public function search(
        string $query,
        array $filters = [],
        string $sortBy = 'relevance',
        string $sortOrder = 'DESC',
        int $page = 1,
        int $perPage = 20
    ): array {
        $db = \Config\Database::connect();
        $builder = $db->table('apps');
        
        // Base query - only approved apps
        $builder->where('approval_status', 'approved');
        
        // Apply search query with relevance scoring
        if (!empty($query)) {
            $query = trim($query);
            $escapedQuery = $db->escapeLikeString($query);
            
            // Calculate relevance score
            // Name match: 3x weight
            // Developer name match: 2x weight
            // Description match: 1x weight
            $builder->select('apps.*');
            $builder->select("(
                (CASE WHEN LOWER(name) LIKE LOWER('%{$escapedQuery}%') THEN 3 ELSE 0 END) +
                (CASE WHEN LOWER(developer_name) LIKE LOWER('%{$escapedQuery}%') THEN 2 ELSE 0 END) +
                (CASE WHEN LOWER(description) LIKE LOWER('%{$escapedQuery}%') THEN 1 ELSE 0 END)
            ) as relevance_score", false);
            
            $builder->groupStart()
                    ->like('LOWER(name)', strtolower($escapedQuery))
                    ->orLike('LOWER(developer_name)', strtolower($escapedQuery))
                    ->orLike('LOWER(description)', strtolower($escapedQuery))
                    ->groupEnd();
        } else {
            $builder->select('apps.*, 0 as relevance_score');
        }
        
        // Apply category filter
        if (!empty($filters['category_id'])) {
            $appIds = $db->table('app_categories')
                        ->select('app_id')
                        ->where('category_id', $filters['category_id'])
                        ->get()
                        ->getResultArray();
            
            $appIds = array_column($appIds, 'app_id');
            
            if (!empty($appIds)) {
                $builder->whereIn('apps.id', $appIds);
            } else {
                // No apps in this category, return empty result
                return [
                    'data' => [],
                    'query' => $query,
                    'filters' => $filters,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => 0,
                        'total_pages' => 0,
                    ],
                ];
            }
        }
        
        // Apply platform filter
        if (!empty($filters['platform_type'])) {
            $builder->where('platform_type', $filters['platform_type']);
        }
        
        // Apply price filter
        if (isset($filters['price_min'])) {
            $builder->where('price >=', $filters['price_min']);
        }
        
        if (isset($filters['price_max'])) {
            $builder->where('price <=', $filters['price_max']);
        }
        
        // Handle free/paid filter
        if (isset($filters['price_type'])) {
            if ($filters['price_type'] === 'free') {
                $builder->where('price', 0);
            } elseif ($filters['price_type'] === 'paid') {
                $builder->where('price >', 0);
            }
        }
        
        // Count total results before pagination
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);
        
        // Apply sorting
        switch ($sortBy) {
            case 'relevance':
                if (!empty($query)) {
                    $builder->orderBy('relevance_score', 'DESC');
                    $builder->orderBy('trust_score', 'DESC'); // Secondary sort
                } else {
                    // If no query, sort by trust score
                    $builder->orderBy('trust_score', 'DESC');
                }
                break;
                
            case 'trust_score':
                $builder->orderBy('trust_score', $sortOrder);
                break;
                
            case 'created_at':
            case 'date':
                $builder->orderBy('created_at', $sortOrder);
                break;
                
            case 'name':
                $builder->orderBy('name', $sortOrder);
                break;
                
            default:
                $builder->orderBy('trust_score', 'DESC');
        }
        
        // Apply pagination
        $offset = ($page - 1) * $perPage;
        $builder->limit($perPage, $offset);
        
        // Execute query
        $results = $builder->get()->getResultArray();
        
        // Highlight search terms in results
        if (!empty($query) && !empty($results)) {
            foreach ($results as &$result) {
                $result['name_highlighted'] = $this->highlightMatches($result['name'], $query);
                $result['developer_name_highlighted'] = $this->highlightMatches($result['developer_name'], $query);
                $result['description_highlighted'] = $this->highlightMatches($result['description'] ?? '', $query);
            }
        }
        
        return [
            'data' => $results,
            'query' => $query,
            'filters' => $filters,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Build search query string for SQL
     * 
     * @param string $query
     * @return string
     */
    public function buildSearchQuery(string $query): string
    {
        $query = trim($query);
        $query = preg_replace('/\s+/', ' ', $query); // Normalize whitespace
        return $query;
    }
    
    /**
     * Highlight matching text in search results
     * 
     * @param string $text Text to highlight
     * @param string $query Search query
     * @return string Text with highlighted matches
     */
    public function highlightMatches(string $text, string $query): string
    {
        if (empty($text) || empty($query)) {
            return esc($text);
        }
        
        $query = trim($query);
        
        // Escape the text for HTML
        $text = esc($text);
        
        // Split query into words
        $words = explode(' ', $query);
        
        foreach ($words as $word) {
            if (strlen($word) < 2) {
                continue; // Skip very short words
            }
            
            // Escape special regex characters
            $word = preg_quote($word, '/');
            
            // Highlight matches (case-insensitive)
            $text = preg_replace(
                '/(' . $word . ')/i',
                '<mark class="search-highlight">$1</mark>',
                $text
            );
        }
        
        return $text;
    }
    
    /**
     * Get search suggestions when no results found
     * 
     * @param string $query
     * @return array Suggested search terms
     */
    public function getSuggestions(string $query): array
    {
        $suggestions = [];
        
        // Get popular categories
        $db = \Config\Database::connect();
        $categories = $db->table('categories')
                        ->select('name')
                        ->orderBy('display_order', 'ASC')
                        ->limit(5)
                        ->get()
                        ->getResultArray();
        
        foreach ($categories as $category) {
            $suggestions[] = $category['name'];
        }
        
        // Get popular developers
        $developers = $db->table('apps')
                        ->select('developer_name')
                        ->where('approval_status', 'approved')
                        ->groupBy('developer_name')
                        ->orderBy('COUNT(*)', 'DESC')
                        ->limit(3)
                        ->get()
                        ->getResultArray();
        
        foreach ($developers as $developer) {
            $suggestions[] = $developer['developer_name'];
        }
        
        return array_unique($suggestions);
    }
    
    /**
     * Get popular search terms (placeholder for future implementation)
     * 
     * @param int $limit
     * @return array
     */
    public function getPopularSearches(int $limit = 10): array
    {
        // This would require a search_logs table to track searches
        // For now, return empty array
        return [];
    }
}
