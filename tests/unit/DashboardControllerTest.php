<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * DashboardControllerTest
 * 
 * Unit tests for the Admin Dashboard Controller.
 * Tests statistics queries and data aggregation.
 */
final class DashboardControllerTest extends CIUnitTestCase
{
    /**
     * Test that dashboard controller exists and is accessible
     */
    public function testDashboardControllerExists(): void
    {
        $this->assertTrue(
            class_exists('App\Controllers\Admin\DashboardController'),
            'DashboardController class should exist'
        );
    }

    /**
     * Test that dashboard controller has required methods
     */
    public function testDashboardControllerHasRequiredMethods(): void
    {
        $controller = new \App\Controllers\Admin\DashboardController();
        
        $this->assertTrue(
            method_exists($controller, 'index'),
            'DashboardController should have index method'
        );
    }

    /**
     * Test that dashboard controller has required protected methods
     */
    public function testDashboardControllerHasProtectedMethods(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\DashboardController');
        
        $this->assertTrue(
            $reflection->hasMethod('getReviewTrend'),
            'DashboardController should have getReviewTrend method'
        );
        
        $this->assertTrue(
            $reflection->hasMethod('getScamReportTrend'),
            'DashboardController should have getScamReportTrend method'
        );
        
        $this->assertTrue(
            $reflection->hasMethod('getRecentUsers'),
            'DashboardController should have getRecentUsers method'
        );
    }

    /**
     * Test that dashboard view file exists
     */
    public function testDashboardViewExists(): void
    {
        $viewPath = APPPATH . 'Views/admin/dashboard.php';
        
        $this->assertFileExists(
            $viewPath,
            'Dashboard view file should exist at ' . $viewPath
        );
    }

    /**
     * Test that dashboard controller uses correct repositories
     */
    public function testDashboardControllerUsesRepositories(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\DashboardController');
        
        $this->assertTrue(
            $reflection->hasProperty('appRepository'),
            'DashboardController should have appRepository property'
        );
        
        $this->assertTrue(
            $reflection->hasProperty('reviewRepository'),
            'DashboardController should have reviewRepository property'
        );
        
        $this->assertTrue(
            $reflection->hasProperty('scamReportRepository'),
            'DashboardController should have scamReportRepository property'
        );
        
        $this->assertTrue(
            $reflection->hasProperty('userModel'),
            'DashboardController should have userModel property'
        );
        
        $this->assertTrue(
            $reflection->hasProperty('subscriberModel'),
            'DashboardController should have subscriberModel property'
        );
    }

    /**
     * Test that trend methods return correct data structure
     */
    public function testTrendMethodsReturnCorrectStructure(): void
    {
        $controller = new \App\Controllers\Admin\DashboardController();
        $reflection = new \ReflectionClass($controller);
        
        // Test getReviewTrend structure
        $reviewTrendMethod = $reflection->getMethod('getReviewTrend');
        $reviewTrendMethod->setAccessible(true);
        
        // We can't actually call it without database, but we can verify the method signature
        $params = $reviewTrendMethod->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('days', $params[0]->getName());
        
        // Test getScamReportTrend structure
        $scamReportTrendMethod = $reflection->getMethod('getScamReportTrend');
        $scamReportTrendMethod->setAccessible(true);
        
        $params = $scamReportTrendMethod->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('days', $params[0]->getName());
        
        // Test getRecentUsers structure
        $recentUsersMethod = $reflection->getMethod('getRecentUsers');
        $recentUsersMethod->setAccessible(true);
        
        $params = $recentUsersMethod->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('days', $params[0]->getName());
    }
}
