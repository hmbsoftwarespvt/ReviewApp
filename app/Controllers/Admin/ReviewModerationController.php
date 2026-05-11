<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ReviewRepository;
use App\Services\TrustScoreService;

/**
 * ReviewModerationController
 * 
 * Admin interface for moderating user reviews.
 * 
 * Features:
 * - View all pending reviews
 * - Approve, reject, or delete reviews
 * - Filter by status, rating, and date
 * - Trigger trust score recalculation on approval
 */
class ReviewModerationController extends BaseController
{
    protected ReviewRepository $reviewRepository;
    protected TrustScoreService $trustScoreService;
    
    public function __construct()
    {
        $this->reviewRepository = new ReviewRepository();
        $this->trustScoreService = new TrustScoreService();
    }
    
    /**
     * Display review moderation list
     * 
     * @return string
     */
    public function index(): string
    {
        // Get filter parameters from query string
        $status = $this->request->getGet('status') ?? 'pending';
        $rating = $this->request->getGet('rating') ?? null;
        $dateFrom = $this->request->getGet('date_from') ?? null;
        $dateTo = $this->request->getGet('date_to') ?? null;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;
        
        // Build query based on filters
        $reviews = $this->getFilteredReviews($status, $rating, $dateFrom, $dateTo, $page, $perPage);
        
        $data = [
            'title' => 'Review Moderation',
            'reviews' => $reviews['data'],
            'pagination' => $reviews['pagination'],
            'filters' => [
                'status' => $status,
                'rating' => $rating,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ];
        
        return view('admin/reviews/index', $data);
    }
    
    /**
     * Approve a review
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function approve(int $id)
    {
        $review = $this->reviewRepository->find($id);
        
        if (!$review) {
            return redirect()->back()->with('error', 'Review not found.');
        }
        
        // Update review status to approved
        $success = $this->reviewRepository->updateStatus($id, 'approved');
        
        if ($success) {
            // Trigger trust score recalculation for the app
            $this->trustScoreService->invalidateCache($review['app_id']);
            $this->trustScoreService->calculateTrustScore($review['app_id']);
            
            return redirect()->back()->with('success', 'Review approved successfully. Trust score recalculated.');
        }
        
        return redirect()->back()->with('error', 'Failed to approve review.');
    }
    
    /**
     * Reject a review
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function reject(int $id)
    {
        $review = $this->reviewRepository->find($id);
        
        if (!$review) {
            return redirect()->back()->with('error', 'Review not found.');
        }
        
        // Update review status to rejected
        $success = $this->reviewRepository->updateStatus($id, 'rejected');
        
        if ($success) {
            return redirect()->back()->with('success', 'Review rejected successfully.');
        }
        
        return redirect()->back()->with('error', 'Failed to reject review.');
    }
    
    /**
     * Delete a review permanently
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete(int $id)
    {
        $review = $this->reviewRepository->find($id);
        
        if (!$review) {
            return redirect()->back()->with('error', 'Review not found.');
        }
        
        // Store app_id before deletion for trust score recalculation
        $appId = $review['app_id'];
        
        // Delete the review
        $success = $this->reviewRepository->delete($id);
        
        if ($success) {
            // Trigger trust score recalculation for the app
            $this->trustScoreService->invalidateCache($appId);
            $this->trustScoreService->calculateTrustScore($appId);
            
            return redirect()->back()->with('success', 'Review deleted successfully. Trust score recalculated.');
        }
        
        return redirect()->back()->with('error', 'Failed to delete review.');
    }
    
    /**
     * Get filtered reviews based on criteria
     * 
     * @param string|null $status
     * @param int|null $rating
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @param int $page
     * @param int $perPage
     * @return array
     */
    protected function getFilteredReviews(?string $status, ?int $rating, ?string $dateFrom, ?string $dateTo, int $page, int $perPage): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('reviews')
                     ->select('reviews.*, users.username, users.email, apps.name as app_name, apps.slug as app_slug')
                     ->join('users', 'users.id = reviews.user_id')
                     ->join('apps', 'apps.id = reviews.app_id');
        
        // Apply status filter
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $builder->where('reviews.approval_status', $status);
        }
        
        // Apply rating filter
        if ($rating && $rating >= 1 && $rating <= 5) {
            $builder->where('reviews.rating', $rating);
        }
        
        // Apply date range filter
        if ($dateFrom) {
            $builder->where('DATE(reviews.created_at) >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('DATE(reviews.created_at) <=', $dateTo);
        }
        
        // Get total count for pagination
        $total = $builder->countAllResults(false);
        
        // Apply pagination
        $offset = ($page - 1) * $perPage;
        $builder->orderBy('reviews.created_at', 'ASC')
               ->limit($perPage, $offset);
        
        $reviews = $builder->get()->getResultArray();
        
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
}

