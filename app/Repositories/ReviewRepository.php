<?php

namespace App\Repositories;

use App\Models\ReviewModel;
use App\Models\ReviewHelpfulVoteModel;

/**
 * ReviewRepository
 * 
 * Data access abstraction layer for reviews.
 * Provides consistent interface for review-related database operations.
 */
class ReviewRepository
{
    protected ReviewModel $reviewModel;
    protected ReviewHelpfulVoteModel $voteModel;
    
    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
        $this->voteModel = new ReviewHelpfulVoteModel();
    }
    
    /**
     * Find review by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        return $this->reviewModel->find($id);
    }
    
    /**
     * Get reviews by app
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
        
        $reviews = $this->reviewModel->getByAppWithUser($appId, $status, $perPage, $offset);
        
        $total = $this->reviewModel->where('app_id', $appId)
                                  ->where('approval_status', $status)
                                  ->countAllResults(false);
        
        return [
            'data' => $reviews,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Get reviews by user
     * 
     * @param int $userId
     * @return array
     */
    public function getByUser(int $userId): array
    {
        return $this->reviewModel->getByUser($userId);
    }
    
    /**
     * Get pending reviews
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPending(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $reviews = $this->reviewModel->getPending($perPage, $offset);
        
        // Enrich with user and app details
        $db = \Config\Database::connect();
        $enrichedReviews = [];
        
        foreach ($reviews as $review) {
            $details = $db->table('reviews')
                         ->select('reviews.*, users.username, users.email, apps.name as app_name, apps.slug as app_slug')
                         ->join('users', 'users.id = reviews.user_id')
                         ->join('apps', 'apps.id = reviews.app_id')
                         ->where('reviews.id', $review['id'])
                         ->get()
                         ->getRowArray();
            
            $enrichedReviews[] = $details;
        }
        
        $total = $this->reviewModel->where('approval_status', 'pending')
                                  ->countAllResults(false);
        
        return [
            'data' => $enrichedReviews,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Create new review
     * 
     * @param array $data
     * @return int Review ID
     */
    public function create(array $data): int
    {
        return $this->reviewModel->insert($data);
    }
    
    /**
     * Update review status
     * 
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->reviewModel->updateStatus($id, $status);
    }
    
    /**
     * Delete review
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->reviewModel->delete($id);
    }
    
    /**
     * Get average rating for app
     * 
     * @param int $appId
     * @return float
     */
    public function getAverageRating(int $appId): float
    {
        return $this->reviewModel->getAverageRating($appId);
    }
    
    /**
     * Get review count for app
     * 
     * @param int $appId
     * @param string $status
     * @return int
     */
    public function getReviewCount(int $appId, string $status = 'approved'): int
    {
        return $this->reviewModel->getReviewCount($appId, $status);
    }
    
    /**
     * Check if user has reviewed app
     * 
     * @param int $userId
     * @param int $appId
     * @return bool
     */
    public function userHasReviewed(int $userId, int $appId): bool
    {
        return $this->reviewModel->userHasReviewed($userId, $appId);
    }
    
    /**
     * Increment helpful count
     * 
     * @param int $reviewId
     * @return bool
     */
    public function incrementHelpfulCount(int $reviewId): bool
    {
        return $this->reviewModel->incrementHelpfulCount($reviewId);
    }
    
    /**
     * Add helpful vote
     * 
     * @param int $reviewId
     * @param int $userId
     * @return bool
     */
    public function addHelpfulVote(int $reviewId, int $userId): bool
    {
        // Check if already voted
        if ($this->voteModel->hasVoted($userId, $reviewId)) {
            return false;
        }
        
        // Add vote
        $result = $this->voteModel->addVote($userId, $reviewId);
        
        // Increment helpful count
        if ($result) {
            $this->incrementHelpfulCount($reviewId);
        }
        
        return $result;
    }
    
    /**
     * Get review with full details
     * 
     * @param int $id
     * @return array|null
     */
    public function getWithDetails(int $id): ?array
    {
        return $this->reviewModel->getWithDetails($id);
    }
    
    /**
     * Get total review count
     * 
     * @param string|null $status
     * @return int
     */
    public function count(?string $status = null): int
    {
        $builder = $this->reviewModel;
        
        if ($status !== null) {
            $builder = $builder->where('approval_status', $status);
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get reviews by rating
     * 
     * @param int $rating
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getByRating(int $rating, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $reviews = $this->reviewModel->where('rating', $rating)
                                    ->where('approval_status', 'approved')
                                    ->orderBy('created_at', 'DESC')
                                    ->limit($perPage, $offset)
                                    ->findAll();
        
        $total = $this->reviewModel->where('rating', $rating)
                                  ->where('approval_status', 'approved')
                                  ->countAllResults(false);
        
        return [
            'data' => $reviews,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }
    
    /**
     * Get recent reviews
     * 
     * @param int $limit
     * @param int $days
     * @return array
     */
    public function getRecent(int $limit = 10, int $days = 7): array
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->reviewModel->where('approval_status', 'approved')
                                ->where('created_at >=', $date)
                                ->orderBy('created_at', 'DESC')
                                ->limit($limit)
                                ->findAll();
    }
}
