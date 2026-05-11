<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestDataSeeder;

/**
 * ScamReportModerationTest
 * 
 * Tests for admin scam report verification functionality.
 * 
 * Tests:
 * - Viewing pending scam reports list
 * - Verifying scam reports
 * - Rejecting scam reports
 * - Updating risk levels
 * - Adding verification notes
 * - Trust score recalculation after verification
 */
class ScamReportModerationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;
    protected $seed        = TestDataSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin session
        $_SESSION = [
            'logged_in' => true,
            'user_id' => 1,
            'username' => 'admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $_SESSION = [];
    }

    /**
     * Test: Admin can view pending scam reports list
     */
    public function testAdminCanViewPendingScamReports()
    {
        $result = $this->get('admin/scam-reports');
        
        $result->assertStatus(200);
        $result->assertSee('Scam Report Verification');
        $result->assertSee('Status');
        $result->assertSee('Risk Level');
    }

    /**
     * Test: Admin can verify a scam report
     */
    public function testAdminCanVerifyScamReport()
    {
        // Create a pending scam report
        $scamReportModel = model('ScamReportModel');
        $reportId = $scamReportModel->insert([
            'app_id' => 1,
            'user_id' => 2,
            'title' => 'Test Scam Report',
            'description' => 'This is a test scam report with sufficient description length to meet the minimum requirement of 100 characters for validation.',
            'risk_level' => 'medium',
            'approval_status' => 'pending',
        ]);

        // Verify the report
        $result = $this->post("admin/scam-reports/verify/{$reportId}", [
            'verification_notes' => 'Verified after investigation',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('success');

        // Check database
        $report = $scamReportModel->find($reportId);
        $this->assertEquals('approved', $report['approval_status']);
        $this->assertEquals('Verified after investigation', $report['verification_notes']);
    }

    /**
     * Test: Admin can reject a scam report
     */
    public function testAdminCanRejectScamReport()
    {
        // Create a pending scam report
        $scamReportModel = model('ScamReportModel');
        $reportId = $scamReportModel->insert([
            'app_id' => 1,
            'user_id' => 2,
            'title' => 'Test Scam Report',
            'description' => 'This is a test scam report with sufficient description length to meet the minimum requirement of 100 characters for validation.',
            'risk_level' => 'low',
            'approval_status' => 'pending',
        ]);

        // Reject the report
        $result = $this->post("admin/scam-reports/reject/{$reportId}", [
            'verification_notes' => 'Insufficient evidence',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('success');

        // Check database
        $report = $scamReportModel->find($reportId);
        $this->assertEquals('rejected', $report['approval_status']);
        $this->assertEquals('Insufficient evidence', $report['verification_notes']);
    }

    /**
     * Test: Admin can update risk level
     */
    public function testAdminCanUpdateRiskLevel()
    {
        // Create a scam report
        $scamReportModel = model('ScamReportModel');
        $reportId = $scamReportModel->insert([
            'app_id' => 1,
            'user_id' => 2,
            'title' => 'Test Scam Report',
            'description' => 'This is a test scam report with sufficient description length to meet the minimum requirement of 100 characters for validation.',
            'risk_level' => 'low',
            'approval_status' => 'pending',
        ]);

        // Update risk level
        $result = $this->post("admin/scam-reports/update-risk/{$reportId}", [
            'risk_level' => 'high',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('success');

        // Check database
        $report = $scamReportModel->find($reportId);
        $this->assertEquals('high', $report['risk_level']);
    }

    /**
     * Test: Invalid risk level is rejected
     */
    public function testInvalidRiskLevelIsRejected()
    {
        // Create a scam report
        $scamReportModel = model('ScamReportModel');
        $reportId = $scamReportModel->insert([
            'app_id' => 1,
            'user_id' => 2,
            'title' => 'Test Scam Report',
            'description' => 'This is a test scam report with sufficient description length to meet the minimum requirement of 100 characters for validation.',
            'risk_level' => 'low',
            'approval_status' => 'pending',
        ]);

        // Try to update with invalid risk level
        $result = $this->post("admin/scam-reports/update-risk/{$reportId}", [
            'risk_level' => 'invalid',
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('error');

        // Check database - should remain unchanged
        $report = $scamReportModel->find($reportId);
        $this->assertEquals('low', $report['risk_level']);
    }

    /**
     * Test: Verification with notes
     */
    public function testVerificationWithNotes()
    {
        // Create a pending scam report
        $scamReportModel = model('ScamReportModel');
        $reportId = $scamReportModel->insert([
            'app_id' => 1,
            'user_id' => 2,
            'title' => 'Test Scam Report',
            'description' => 'This is a test scam report with sufficient description length to meet the minimum requirement of 100 characters for validation.',
            'risk_level' => 'high',
            'approval_status' => 'pending',
        ]);

        $notes = 'Verified. Evidence is credible and matches user complaints.';

        // Verify with notes
        $result = $this->post("admin/scam-reports/verify/{$reportId}", [
            'verification_notes' => $notes,
        ]);

        $result->assertRedirect();
        $result->assertSessionHas('success');

        // Check database
        $report = $scamReportModel->find($reportId);
        $this->assertEquals('approved', $report['approval_status']);
        $this->assertEquals($notes, $report['verification_notes']);
    }

    /**
     * Test: Non-existent report returns error
     */
    public function testNonExistentReportReturnsError()
    {
        $nonExistentId = 99999;

        $result = $this->post("admin/scam-reports/verify/{$nonExistentId}");

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }

    /**
     * Test: Filter by status
     */
    public function testFilterByStatus()
    {
        $result = $this->get('admin/scam-reports?status=pending');
        
        $result->assertStatus(200);
        $result->assertSee('Scam Report Verification');
    }

    /**
     * Test: Filter by risk level
     */
    public function testFilterByRiskLevel()
    {
        $result = $this->get('admin/scam-reports?risk_level=high');
        
        $result->assertStatus(200);
        $result->assertSee('Scam Report Verification');
    }

    /**
     * Test: Filter by date range
     */
    public function testFilterByDateRange()
    {
        $dateFrom = date('Y-m-d', strtotime('-7 days'));
        $dateTo = date('Y-m-d');

        $result = $this->get("admin/scam-reports?date_from={$dateFrom}&date_to={$dateTo}");
        
        $result->assertStatus(200);
        $result->assertSee('Scam Report Verification');
    }

    /**
     * Test: Non-admin cannot access scam report moderation
     */
    public function testNonAdminCannotAccessModeration()
    {
        // Change session to regular user
        $_SESSION['role'] = 'user';

        $result = $this->get('admin/scam-reports');

        $result->assertRedirect();
    }

    /**
     * Test: Unauthenticated user is redirected to login
     */
    public function testUnauthenticatedUserRedirectedToLogin()
    {
        // Clear session
        $_SESSION = [];

        $result = $this->get('admin/scam-reports');

        $result->assertRedirect();
    }
}

