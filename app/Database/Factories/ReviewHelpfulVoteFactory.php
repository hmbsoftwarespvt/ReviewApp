<?php

namespace App\Database\Factories;

use App\Models\ReviewHelpfulVoteModel;

/**
 * ReviewHelpfulVoteFactory
 * 
 * Generates test data for ReviewHelpfulVote model.
 */
class ReviewHelpfulVoteFactory extends BaseFactory
{
    /**
     * Generate review helpful vote data
     */
    public function make(array $overrides = []): array
    {
        $data = [
            'review_id' => null, // Must be provided
            'user_id'   => null, // Must be provided
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Create multiple votes for a review from different users
     */
    public function createVotesForReview(int $reviewId, array $userIds): array
    {
        $model = $this->getModel();
        $ids = [];

        foreach ($userIds as $userId) {
            // Check if vote already exists to avoid duplicates
            if (!$model->hasVoted($userId, $reviewId)) {
                $data = $this->make([
                    'review_id' => $reviewId,
                    'user_id' => $userId,
                ]);
                
                $id = $model->insert($data);
                if ($id !== false) {
                    $ids[] = $id;
                }
            }
        }

        return $ids;
    }

    protected function getModel()
    {
        return new ReviewHelpfulVoteModel();
    }
}
