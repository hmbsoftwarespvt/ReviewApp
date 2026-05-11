<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ReviewModel
 * 
 * Manages user reviews for apps with moderation workflow.
 * 
 * Relationships:
 * - belongsTo: app (AppModel)
 * - belongsTo: user (UserModel)
 * - hasMany: review_helpful_votes (ReviewHelpfulVoteModel)
 */
class ReviewModel extends Model
{
    protected $table            = 'reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'app_id',
        'user_id',
        'rating',
        'title',
        'review_text',
        'pros',
        'cons',
        'approval_status',
        'helpful_count',
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
        'rating'      => 'required|integer|greater_than[0]|less_than[6]',
        'title'       => 'required|max_length[255]',
        'review_text' => 'required|min_length[50]|max_length[2000]',
        'pros'        => 'permit_empty|max_length[1000]',
        'cons'        => 'permit_empty|max_length[1000]',
        'approval_status' => 'permit_empty|in_list[pending,approved,rejected]',
    ];

    protected $validationMessages = [
        'rating' => [
            'required'      => 'Rating is required',
            'greater_than'  => 'Rating must be between 1 and 5',
            'less_than'     => 'Rating must be between 1 and 5',
        ],
        'title' => [
            'required'   => 'Review title is required',
            'max_length' => 'Title cannot exceed 255 characters',
        ],
        'review_text' => [
            'required'   => 'Review text is required',
            'min_length' => 'Review must be at least 50 characters',
            'max_length' => 'Review cannot exceed 2000 characters',
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
     * Get reviews by app
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
     * Get reviews by user
     */
    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get pending reviews
     */
    public function getPending(int $limit = 20, int $offset = 0): array
    {
        return $this->where('approval_status', 'pending')
                    ->orderBy('created_at', 'ASC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Get average rating for app
     */
    public function getAverageRating(int $appId): float
    {
        $result = $this->selectAvg('rating', 'avg_rating')
                      ->where('app_id', $appId)
                      ->where('approval_status', 'approved')
                      ->first();
        
        return $result ? (float) $result['avg_rating'] : 0.0;
    }

    /**
     * Get review count for app
     */
    public function getReviewCount(int $appId, string $status = 'approved'): int
    {
        return $this->where('app_id', $appId)
                    ->where('approval_status', $status)
                    ->countAllResults();
    }

    /**
     * Check if user has reviewed app
     */
    public function userHasReviewed(int $userId, int $appId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('app_id', $appId)
                    ->countAllResults() > 0;
    }

    /**
     * Increment helpful count
     */
    public function incrementHelpfulCount(int $reviewId): bool
    {
        return $this->set('helpful_count', 'helpful_count + 1', false)
                    ->where('id', $reviewId)
                    ->update();
    }

    /**
     * Update approval status
     */
    public function updateStatus(int $reviewId, string $status): bool
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }
        
        return $this->update($reviewId, ['approval_status' => $status]);
    }

    /**
     * Get review with user and app details
     */
    public function getWithDetails(int $reviewId): ?array
    {
        $db = \Config\Database::connect();
        
        return $db->table('reviews')
                  ->select('reviews.*, users.username, users.email, apps.name as app_name, apps.slug as app_slug')
                  ->join('users', 'users.id = reviews.user_id')
                  ->join('apps', 'apps.id = reviews.app_id')
                  ->where('reviews.id', $reviewId)
                  ->get()
                  ->getRowArray();
    }

    /**
     * Get reviews with user details for app
     */
    public function getByAppWithUser(int $appId, string $status = 'approved', int $limit = 10, int $offset = 0): array
    {
        $db = \Config\Database::connect();
        
        return $db->table('reviews')
                  ->select('reviews.*, users.username')
                  ->join('users', 'users.id = reviews.user_id')
                  ->where('reviews.app_id', $appId)
                  ->where('reviews.approval_status', $status)
                  ->orderBy('reviews.created_at', 'DESC')
                  ->limit($limit, $offset)
                  ->get()
                  ->getResultArray();
    }
}
