<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ReviewHelpfulVoteModel
 * 
 * Manages helpful votes for reviews (prevents duplicate votes).
 * 
 * Relationships:
 * - belongsTo: review (ReviewModel)
 * - belongsTo: user (UserModel)
 */
class ReviewHelpfulVoteModel extends Model
{
    protected $table            = 'review_helpful_votes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'review_id',
        'user_id',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'review_id' => 'required|integer|is_not_unique[reviews.id]',
        'user_id'   => 'required|integer|is_not_unique[users.id]',
    ];

    protected $validationMessages = [
        'review_id' => [
            'required' => 'Review ID is required',
        ],
        'user_id' => [
            'required' => 'User ID is required',
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
     * Check if user has voted for review
     */
    public function hasVoted(int $userId, int $reviewId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('review_id', $reviewId)
                    ->countAllResults() > 0;
    }

    /**
     * Add vote (if not already voted)
     */
    public function addVote(int $userId, int $reviewId): bool
    {
        if ($this->hasVoted($userId, $reviewId)) {
            return false;
        }
        
        $result = $this->insert([
            'user_id'   => $userId,
            'review_id' => $reviewId,
        ]);
        
        if ($result) {
            // Increment helpful count in reviews table
            $reviewModel = new \App\Models\ReviewModel();
            $reviewModel->incrementHelpfulCount($reviewId);
        }
        
        return $result !== false;
    }

    /**
     * Remove vote
     */
    public function removeVote(int $userId, int $reviewId): bool
    {
        $vote = $this->where('user_id', $userId)
                    ->where('review_id', $reviewId)
                    ->first();
        
        if (!$vote) {
            return false;
        }
        
        $result = $this->delete($vote['id']);
        
        if ($result) {
            // Decrement helpful count in reviews table
            $reviewModel = new \App\Models\ReviewModel();
            $reviewModel->set('helpful_count', 'helpful_count - 1', false)
                       ->where('id', $reviewId)
                       ->where('helpful_count >', 0)
                       ->update();
        }
        
        return $result;
    }

    /**
     * Get vote count for review
     */
    public function getVoteCount(int $reviewId): int
    {
        return $this->where('review_id', $reviewId)->countAllResults();
    }

    /**
     * Get all votes by user
     */
    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all votes for review
     */
    public function getByReview(int $reviewId): array
    {
        return $this->where('review_id', $reviewId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
