<?php

namespace Tests\Unit;

use App\Controllers\Admin\ReviewModerationController;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ReviewModerationTest extends CIUnitTestCase
{
    /**
     * Test ReviewModerationController exists
     */
    public function testReviewModerationControllerExists(): void
    {
        $this->assertTrue(class_exists('App\Controllers\Admin\ReviewModerationController'));
    }

    /**
     * Test controller has required methods
     */
    public function testControllerHasRequiredMethods(): void
    {
        $this->assertTrue(method_exists('App\Controllers\Admin\ReviewModerationController', 'index'));
        $this->assertTrue(method_exists('App\Controllers\Admin\ReviewModerationController', 'approve'));
        $this->assertTrue(method_exists('App\Controllers\Admin\ReviewModerationController', 'reject'));
        $this->assertTrue(method_exists('App\Controllers\Admin\ReviewModerationController', 'delete'));
    }

    /**
     * Test view file exists
     */
    public function testViewFileExists(): void
    {
        $viewPath = APPPATH . 'Views/admin/reviews/index.php';
        $this->assertFileExists($viewPath);
    }

    /**
     * Test ReviewRepository has required methods for moderation
     */
    public function testReviewRepositoryHasRequiredMethods(): void
    {
        $this->assertTrue(class_exists('App\Repositories\ReviewRepository'));
        $this->assertTrue(method_exists('App\Repositories\ReviewRepository', 'getPending'));
        $this->assertTrue(method_exists('App\Repositories\ReviewRepository', 'updateStatus'));
        $this->assertTrue(method_exists('App\Repositories\ReviewRepository', 'delete'));
        $this->assertTrue(method_exists('App\Repositories\ReviewRepository', 'getByRating'));
    }

    /**
     * Test TrustScoreService has required methods for recalculation
     */
    public function testTrustScoreServiceHasRequiredMethods(): void
    {
        $this->assertTrue(class_exists('App\Services\TrustScoreService'));
        $this->assertTrue(method_exists('App\Services\TrustScoreService', 'calculateTrustScore'));
        $this->assertTrue(method_exists('App\Services\TrustScoreService', 'invalidateCache'));
    }
}
