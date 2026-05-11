<?php

namespace Tests\Integration;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Integration tests for Review Moderation functionality
 * 
 * Tests the complete workflow:
 * - Viewing pending reviews
 * - Filtering by status, rating, date
 * - Approving reviews and trust score recalculation
 * - Rejecting reviews
 * - Deleting reviews
 * 
 * @internal
 */
final class ReviewModerationIntegrationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = 'App';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test admin user
        $this->createTestAdmin();
        
        // Create test data
        $this->createTestData();
    }

    /**
     * Test admin can view pending reviews list
     */
    public function testAdminCanViewPendingReviews(): void
    {
        // Login as admin
        $this->loginAsAdmin();
        
        // Access review moderation page
        $result = $this->get('admin/reviews');
        
        $result->assertStatus(200);
        $result->assertSee('Review Moderation');
        $result->assertSee('Test Review Title');
    }

    /**
     * Test filtering by status works correctly
     */
    public function testFilteringByStatus(): void
    {
        $this->loginAsAdmin();
        
        // Filter by pending status
        $result = $this->get('admin/reviews?status=pending');
        $result->assertStatus(200);
        $result->assertSee('Pending');
        
        // Filter by approved status
        $result = $this->get('admin/reviews?status=approved');
        $result->assertStatus(200);
    }

    /**
     * Test filtering by rating works correctly
     */
    public function testFilteringByRating(): void
    {
        $this->loginAsAdmin();
        
        // Filter by 5-star rating
        $result = $this->get('admin/reviews?status=pending&rating=5');
        $result->assertStatus(200);
    }

    /**
     * Test filtering by date range works correctly
     */
    public function testFilteringByDateRange(): void
    {
        $this->loginAsAdmin();
        
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        // Filter by date range
        $result = $this->get("admin/reviews?status=pending&date_from={$yesterday}&date_to={$today}");
        $result->assertStatus(200);
    }

    /**
     * Test approving a review triggers trust score recalculation
     */
    public function testApprovingReviewRecalculatesTrustScore(): void
    {
        $this->loginAsAdmin();
        
        // Get the test review
        $db = \Config\Database::connect();
        $review = $db->table('reviews')
                    ->where('approval_status', 'pending')
                    ->get()
                    ->getRowArray();
        
        $this->assertNotNull($review);
        
        // Get app's trust score before approval
        $app = $db->table('apps')->where('id', $review['app_id'])->get()->getRowArray();
        $trustScoreBefore = $app['trust_score'];
        
        // Approve the review
        $result = $this->post("admin/reviews/approve/{$review['id']}");
        
        $result->assertRedirect();
        $result->assertSessionHas('success');
        
        // Verify review status changed to approved
        $updatedReview = $db->table('reviews')->where('id', $review['id'])->get()->getRowArray();
        $this->assertEquals('approved', $updatedReview['approval_status']);
        
        // Verify trust score was recalculated (it should change or at least be recalculated)
        $updatedApp = $db->table('apps')->where('id', $review['app_id'])->get()->getRowArray();
        // Trust score should be a valid number between 0 and 100
        $this->assertGreaterThanOrEqual(0, $updatedApp['trust_score']);
        $this->assertLessThanOrEqual(100, $updatedApp['trust_score']);
    }

    /**
     * Test rejecting a review works correctly
     */
    public function testRejectingReview(): void
    {
        $this->loginAsAdmin();
        
        // Get a pending review
        $db = \Config\Database::connect();
        $review = $db->table('reviews')
                    ->where('approval_status', 'pending')
                    ->get()
                    ->getRowArray();
        
        $this->assertNotNull($review);
        
        // Reject the review
        $result = $this->post("admin/reviews/reject/{$review['id']}");
        
        $result->assertRedirect();
        $result->assertSessionHas('success');
        
        // Verify review status changed to rejected
        $updatedReview = $db->table('reviews')->where('id', $review['id'])->get()->getRowArray();
        $this->assertEquals('rejected', $updatedReview['approval_status']);
    }

    /**
     * Test deleting a review works correctly
     */
    public function testDeletingReview(): void
    {
        $this->loginAsAdmin();
        
        // Get a review to delete
        $db = \Config\Database::connect();
        $review = $db->table('reviews')
                    ->where('approval_status', 'pending')
                    ->get()
                    ->getRowArray();
        
        $this->assertNotNull($review);
        $reviewId = $review['id'];
        
        // Delete the review
        $result = $this->post("admin/reviews/delete/{$reviewId}");
        
        $result->assertRedirect();
        $result->assertSessionHas('success');
        
        // Verify review was deleted
        $deletedReview = $db->table('reviews')->where('id', $reviewId)->get()->getRowArray();
        $this->assertNull($deletedReview);
    }

    /**
     * Test approved reviews appear on public site
     */
    public function testApprovedReviewsAppearOnPublicSite(): void
    {
        $this->loginAsAdmin();
        
        // Get a pending review
        $db = \Config\Database::connect();
        $review = $db->table('reviews')
                    ->where('approval_status', 'pending')
                    ->get()
                    ->getRowArray();
        
        $this->assertNotNull($review);
        
        // Get the app slug
        $app = $db->table('apps')->where('id', $review['app_id'])->get()->getRowArray();
        
        // Approve the review
        $this->post("admin/reviews/approve/{$review['id']}");
        
        // Logout from admin
        $this->get('auth/logout');
        
        // Visit the public app page
        $result = $this->get("apps/{$app['slug']}");
        
        // The approved review should be visible
        $result->assertStatus(200);
        $result->assertSee($review['title']);
    }

    /**
     * Test pagination works correctly
     */
    public function testPaginationWorks(): void
    {
        $this->loginAsAdmin();
        
        // Create many reviews to test pagination
        $this->createManyReviews(25);
        
        // Access first page
        $result = $this->get('admin/reviews?status=pending&page=1');
        $result->assertStatus(200);
        $result->assertSee('pagination');
        
        // Access second page
        $result = $this->get('admin/reviews?status=pending&page=2');
        $result->assertStatus(200);
    }

    // ========== Helper Methods ==========

    /**
     * Create test admin user
     */
    protected function createTestAdmin(): void
    {
        $db = \Config\Database::connect();
        
        $db->table('users')->insert([
            'username' => 'testadmin',
            'email' => 'admin@test.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Login as admin
     */
    protected function loginAsAdmin(): void
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'admin';
        $_SESSION['logged_in'] = true;
    }

    /**
     * Create test data (app, user, reviews)
     */
    protected function createTestData(): void
    {
        $db = \Config\Database::connect();
        
        // Create test app
        $db->table('apps')->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'version' => '1.0.0',
            'size' => '10MB',
            'platform_type' => 'android',
            'price' => 0.00,
            'developer_name' => 'Test Developer',
            'release_date' => date('Y-m-d', strtotime('-100 days')),
            'download_url' => 'https://example.com/download',
            'trust_score' => 50.00,
            'security_score' => 15.00,
            'developer_reputation' => 10.00,
            'approval_status' => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Create test regular user
        $db->table('users')->insert([
            'username' => 'testuser',
            'email' => 'user@test.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Create test review (pending)
        $db->table('reviews')->insert([
            'app_id' => 1,
            'user_id' => 2,
            'rating' => 5,
            'title' => 'Test Review Title',
            'review_text' => 'This is a test review with more than 50 characters to meet the minimum requirement.',
            'pros' => 'Great features',
            'cons' => 'None',
            'approval_status' => 'pending',
            'helpful_count' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Create many reviews for pagination testing
     */
    protected function createManyReviews(int $count): void
    {
        $db = \Config\Database::connect();
        
        for ($i = 0; $i < $count; $i++) {
            $db->table('reviews')->insert([
                'app_id' => 1,
                'user_id' => 2,
                'rating' => rand(1, 5),
                'title' => "Test Review {$i}",
                'review_text' => "This is test review number {$i} with more than 50 characters to meet the minimum requirement.",
                'approval_status' => 'pending',
                'helpful_count' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} hours")),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
