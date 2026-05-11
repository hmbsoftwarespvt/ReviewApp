<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\UserModel;
use App\Models\AppModel;
use App\Models\ReviewModel;

/**
 * ReviewSubmissionTest
 * 
 * Functional tests for Task 27: Review Submission
 * 
 * Tests cover:
 * - Review submission form display
 * - Review validation (rating 1-5, text 50-2000 chars)
 * - Duplicate review prevention
 * - Pending status assignment
 * - Success message display
 * - Pending review indicator
 */
class ReviewSubmissionTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected $seed = 'TestSeeder';

    protected UserModel $userModel;
    protected AppModel $appModel;
    protected ReviewModel $reviewModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new UserModel();
        $this->appModel = new AppModel();
        $this->reviewModel = new ReviewModel();
    }

    /**
     * Test: Authenticated users can see review submission form
     */
    public function testAuthenticatedUserSeesReviewForm()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Simulate logged-in user
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;

        // Visit app detail page
        $result = $this->get('apps/test-app');

        // Assert review form is present
        $result->assertSee('Write a Review');
        $result->assertSee('Rating');
        $result->assertSee('Review Title');
        $result->assertSee('Your Review');
        $result->assertSee('Submit Review');
    }

    /**
     * Test: Unauthenticated users see login prompt
     */
    public function testUnauthenticatedUserSeesLoginPrompt()
    {
        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Visit app detail page without login
        $result = $this->get('apps/test-app');

        // Assert login prompt is present
        $result->assertSee('Login');
        $result->assertSee('register');
        $result->assertDontSee('Write a Review');
    }

    /**
     * Test: Valid review submission succeeds
     */
    public function testValidReviewSubmissionSucceeds()
    {
        // Create test user
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Simulate logged-in user
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;

        // Submit review
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 4,
            'title' => 'Great app!',
            'review_text' => 'This is a great app with excellent features. I highly recommend it to everyone who needs this functionality.',
            'pros' => 'Easy to use, fast performance',
            'cons' => 'Could use more customization options',
        ]);

        // Assert redirect with success message
        $result->assertRedirect();
        $this->assertTrue(session()->has('success'));

        // Assert review was created with pending status
        $review = $this->reviewModel->where('user_id', $userId)
                                   ->where('app_id', $appId)
                                   ->first();

        $this->assertNotNull($review);
        $this->assertEquals(4, $review['rating']);
        $this->assertEquals('Great app!', $review['title']);
        $this->assertEquals('pending', $review['approval_status']);
    }

    /**
     * Test: Review validation - rating must be 1-5
     */
    public function testReviewValidationRatingRange()
    {
        // Create test user and app
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;

        // Test invalid rating (0)
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 0,
            'title' => 'Test Review',
            'review_text' => 'This is a test review with at least fifty characters to meet the minimum requirement.',
        ]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('errors'));

        // Test invalid rating (6)
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 6,
            'title' => 'Test Review',
            'review_text' => 'This is a test review with at least fifty characters to meet the minimum requirement.',
        ]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('errors'));
    }

    /**
     * Test: Review validation - text must be 50-2000 characters
     */
    public function testReviewValidationTextLength()
    {
        // Create test user and app
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;

        // Test text too short (< 50 chars)
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 4,
            'title' => 'Test Review',
            'review_text' => 'Too short',
        ]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('errors'));

        // Test text too long (> 2000 chars)
        $longText = str_repeat('a', 2001);
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 4,
            'title' => 'Test Review',
            'review_text' => $longText,
        ]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('errors'));

        // Test valid text length (50-2000 chars)
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 4,
            'title' => 'Test Review',
            'review_text' => 'This is a valid review with exactly fifty characters here and more text to ensure it passes validation.',
        ]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('success'));
    }

    /**
     * Test: Duplicate review prevention
     */
    public function testDuplicateReviewPrevention()
    {
        // Create test user and app
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Create first review
        $this->reviewModel->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'rating' => 4,
            'title' => 'First Review',
            'review_text' => 'This is my first review with at least fifty characters to meet the minimum requirement.',
            'approval_status' => 'pending',
        ]);

        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;

        // Attempt to submit second review
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 5,
            'title' => 'Second Review',
            'review_text' => 'This is my second review attempt with at least fifty characters to meet the minimum requirement.',
        ]);

        // Assert error message
        $result->assertRedirect();
        $this->assertTrue(session()->has('error'));
        $this->assertStringContainsString('already submitted', session()->get('error'));

        // Assert only one review exists
        $reviewCount = $this->reviewModel->where('user_id', $userId)
                                        ->where('app_id', $appId)
                                        ->countAllResults();
        $this->assertEquals(1, $reviewCount);
    }

    /**
     * Test: Review set to pending status
     */
    public function testReviewSetToPendingStatus()
    {
        // Create test user and app
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;

        // Submit review
        $this->post("apps/submit-review/{$appId}", [
            'rating' => 4,
            'title' => 'Test Review',
            'review_text' => 'This is a test review with at least fifty characters to meet the minimum requirement.',
        ]);

        // Assert review has pending status
        $review = $this->reviewModel->where('user_id', $userId)
                                   ->where('app_id', $appId)
                                   ->first();

        $this->assertEquals('pending', $review['approval_status']);
    }

    /**
     * Test: Success message displayed after submission
     */
    public function testSuccessMessageDisplayed()
    {
        // Create test user and app
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;

        // Submit review
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 4,
            'title' => 'Test Review',
            'review_text' => 'This is a test review with at least fifty characters to meet the minimum requirement.',
        ]);

        // Assert success message
        $this->assertTrue(session()->has('success'));
        $this->assertStringContainsString('pending approval', session()->get('success'));
    }

    /**
     * Test: Pending review indicator displayed
     */
    public function testPendingReviewIndicatorDisplayed()
    {
        // Create test user and app
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Create pending review
        $this->reviewModel->insert([
            'app_id' => $appId,
            'user_id' => $userId,
            'rating' => 4,
            'title' => 'Pending Review',
            'review_text' => 'This is a pending review with at least fifty characters to meet the minimum requirement.',
            'approval_status' => 'pending',
        ]);

        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;

        // Visit app detail page
        $result = $this->get('apps/test-app');

        // Assert pending review indicator is present
        $result->assertSee('Your review is pending approval');
        $result->assertSee('Pending Review');
        $result->assertDontSee('Write a Review'); // Form should not be shown
    }

    /**
     * Test: User cannot submit review without authentication
     */
    public function testUnauthenticatedUserCannotSubmitReview()
    {
        // Create test app
        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        // Attempt to submit review without login
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 4,
            'title' => 'Test Review',
            'review_text' => 'This is a test review with at least fifty characters to meet the minimum requirement.',
        ]);

        // Assert redirect to login
        $result->assertRedirect();
        $result->assertRedirectTo('/auth/login');
    }

    /**
     * Test: Review form validates required fields
     */
    public function testReviewFormValidatesRequiredFields()
    {
        // Create test user and app
        $userId = $this->userModel->insert([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'user',
            'status' => 'active',
            'email_verified' => true,
        ]);

        $appId = $this->appModel->insert([
            'name' => 'Test App',
            'slug' => 'test-app',
            'description' => 'Test app description',
            'platform_type' => 'android',
            'developer_name' => 'Test Developer',
            'approval_status' => 'approved',
        ]);

        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $userId;

        // Test missing rating
        $result = $this->post("apps/submit-review/{$appId}", [
            'title' => 'Test Review',
            'review_text' => 'This is a test review with at least fifty characters to meet the minimum requirement.',
        ]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('errors'));

        // Test missing title
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 4,
            'review_text' => 'This is a test review with at least fifty characters to meet the minimum requirement.',
        ]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('errors'));

        // Test missing review_text
        $result = $this->post("apps/submit-review/{$appId}", [
            'rating' => 4,
            'title' => 'Test Review',
        ]);

        $result->assertRedirect();
        $this->assertTrue(session()->has('errors'));
    }
}

