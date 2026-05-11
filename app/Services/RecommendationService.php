<?php

namespace App\Services;

use App\Models\AppModel;
use App\Models\CategoryModel;

/**
 * RecommendationService
 * 
 * Generates similar app recommendations based on category match,
 * trust score proximity, and platform type.
 */
class RecommendationService
{
    protected AppModel $appModel;
    protected CategoryModel $categoryModel;
    protected $cache;
    
    public function __construct()
    {
        $this->appModel = new AppModel();
        $this->categoryModel = new CategoryModel();
        $this->cache = \Config\Services::cache();
    }
    
    /**
     * Get similar apps for a given app
     * 
     * @param int $appId
     * @param int $limit Maximum number of recommendations (default: 6)
     * @return array
     */
    public function getSimilarApps(int $appId, int $limit = 6): array
    {
        // Check cache first
        $cacheKey = "similar_apps_{$appId}_{$limit}";
        $cachedApps = $this->cache->get($cacheKey);
        
        if ($cachedApps !== null) {
            return $cachedApps;
        }
        
        // Get the source app
        $sourceApp = $this->appModel->find($appId);
        
        if (!$sourceApp) {
            return [];
        }
        
        // Get source app categories
        $sourceCategories = $this->appModel->getCategories($appId);
        $sourceCategoryIds = array_column($sourceCategories, 'id');
        
        if (empty($sourceCategoryIds)) {
            // If no categories, return empty
            return [];
        }
        
        // Find apps with matching categories
        $db = \Config\Database::connect();
        $builder = $db->table('apps');
        
        $builder->select('apps.*, COUNT(DISTINCT app_categories.category_id) as category_matches')
                ->join('app_categories', 'app_categories.app_id = apps.id')
                ->whereIn('app_categories.category_id', $sourceCategoryIds)
                ->where('apps.id !=', $appId)
                ->where('apps.approval_status', 'approved')
                ->groupBy('apps.id');
        
        // Get all potential matches
        $potentialMatches = $builder->get()->getResultArray();
        
        // Calculate similarity scores for each app
        $scoredApps = [];
        
        foreach ($potentialMatches as $app) {
            $similarityScore = $this->calculateSimilarity($sourceApp, $app, (int)$app['category_matches']);
            
            $scoredApps[] = [
                'app' => $app,
                'similarity_score' => $similarityScore,
            ];
        }
        
        // Sort by similarity score (descending)
        usort($scoredApps, function($a, $b) {
            return $b['similarity_score'] <=> $a['similarity_score'];
        });
        
        // Take top N apps
        $similarApps = array_slice($scoredApps, 0, $limit);
        
        // Extract just the app data
        $result = array_map(function($item) {
            return $item['app'];
        }, $similarApps);
        
        // If we don't have enough apps, try to fill with apps from related categories
        if (count($result) < $limit) {
            $needed = $limit - count($result);
            $existingIds = array_column($result, 'id');
            $existingIds[] = $appId; // Exclude source app
            
            $additionalApps = $this->getAppsFromRelatedCategories(
                $sourceCategoryIds,
                $existingIds,
                $needed
            );
            
            $result = array_merge($result, $additionalApps);
        }
        
        // Cache for 1 hour
        $this->cache->save($cacheKey, $result, 3600);
        
        return $result;
    }
    
    /**
     * Calculate similarity score between two apps
     * 
     * Scoring:
     * - Category match: +50 points per matching category
     * - Trust score proximity (±10): +30 points
     * - Same platform type: +20 points
     * 
     * @param array $sourceApp
     * @param array $targetApp
     * @param int $categoryMatches Number of matching categories
     * @return float Similarity score
     */
    public function calculateSimilarity(array $sourceApp, array $targetApp, int $categoryMatches = 0): float
    {
        $score = 0;
        
        // Category match: +50 points per matching category
        $score += $categoryMatches * 50;
        
        // Trust score proximity: +30 points if within ±10 points
        $sourceTrustScore = (float)$sourceApp['trust_score'];
        $targetTrustScore = (float)$targetApp['trust_score'];
        $trustScoreDiff = abs($sourceTrustScore - $targetTrustScore);
        
        if ($trustScoreDiff <= 10) {
            $score += 30;
        } elseif ($trustScoreDiff <= 20) {
            $score += 15; // Partial credit for close scores
        }
        
        // Same platform type: +20 points
        if ($sourceApp['platform_type'] === $targetApp['platform_type']) {
            $score += 20;
        }
        
        return $score;
    }
    
    /**
     * Get apps from related categories when not enough similar apps found
     * 
     * @param array $categoryIds
     * @param array $excludeIds App IDs to exclude
     * @param int $limit
     * @return array
     */
    protected function getAppsFromRelatedCategories(array $categoryIds, array $excludeIds, int $limit): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('apps');
        
        $builder->select('apps.*')
                ->join('app_categories', 'app_categories.app_id = apps.id')
                ->whereIn('app_categories.category_id', $categoryIds)
                ->whereNotIn('apps.id', $excludeIds)
                ->where('apps.approval_status', 'approved')
                ->orderBy('apps.trust_score', 'DESC')
                ->groupBy('apps.id')
                ->limit($limit);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Invalidate cached recommendations for an app
     * 
     * @param int $appId
     * @return bool
     */
    public function invalidateCache(int $appId): bool
    {
        // Invalidate all possible cache keys for this app
        $limits = [6, 10, 12]; // Common limit values
        $success = true;
        
        foreach ($limits as $limit) {
            $cacheKey = "similar_apps_{$appId}_{$limit}";
            $success = $success && $this->cache->delete($cacheKey);
        }
        
        return $success;
    }
}
