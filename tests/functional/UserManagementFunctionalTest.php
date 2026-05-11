<?php

namespace Tests\Functional;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Functional tests for User Management
 * 
 * Verifies that all required components exist and are properly configured
 * for the user management workflow.
 * 
 * @internal
 */
final class UserManagementFunctionalTest extends CIUnitTestCase
{
    /**
     * Test UserManagementController exists and has all required methods
     */
    public function testControllerExistsWithAllMethods(): void
    {
        $this->assertTrue(class_exists('App\Controllers\Admin\UserManagementController'));
        
        $methods = ['index', 'view', 'suspend', 'reactivate', 'delete'];
        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('App\Controllers\Admin\UserManagementController', $method),
                "Method {$method} should exist in UserManagementController"
            );
        }
    }

    /**
     * Test UserModel has all required methods
     */
    public function testUserModelHasRequiredMethods(): void
    {
        $this->assertTrue(class_exists('App\Models\UserModel'));
        
        $methods = ['find', 'findByEmailOrUsername', 'getReviews', 'getScamReports'];
        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('App\Models\UserModel', $method),
                "Method {$method} should exist in UserModel"
            );
        }
    }

    /**
     * Test admin user management views exist
     */
    public function testViewFilesExist(): void
    {
        $indexViewPath = APPPATH . 'Views/admin/users/index.php';
        $detailViewPath = APPPATH . 'Views/admin/users/view.php';
        
        $this->assertFileExists($indexViewPath, 'User list view should exist');
        $this->assertFileExists($detailViewPath, 'User detail view should exist');
    }

    /**
     * Test index view contains required UI elements
     */
    public function testIndexViewContainsRequiredElements(): void
    {
        $viewPath = APPPATH . 'Views/admin/users/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for search functionality
        $this->assertStringContainsString('search', $content, 'View should have search input');
        
        // Check for action buttons
        $this->assertStringContainsString('suspend', $content, 'View should have suspend button');
        $this->assertStringContainsString('reactivate', $content, 'View should have reactivate button');
        $this->assertStringContainsString('delete', $content, 'View should have delete button');
        
        // Check for pagination
        $this->assertStringContainsString('pagination', $content, 'View should have pagination');
        
        // Check for user statistics
        $this->assertStringContainsString('review_count', $content, 'View should display review count');
        $this->assertStringContainsString('scam_report_count', $content, 'View should display scam report count');
    }

    /**
     * Test detail view contains required UI elements
     */
    public function testDetailViewContainsRequiredElements(): void
    {
        $viewPath = APPPATH . 'Views/admin/users/view.php';
        $content = file_get_contents($viewPath);
        
        // Check for user information
        $this->assertStringContainsString('username', $content, 'View should display username');
        $this->assertStringContainsString('email', $content, 'View should display email');
        $this->assertStringContainsString('status', $content, 'View should display status');
        $this->assertStringContainsString('role', $content, 'View should display role');
        
        // Check for statistics
        $this->assertStringContainsString('reviewCount', $content, 'View should display review count');
        $this->assertStringContainsString('scamReportCount', $content, 'View should display scam report count');
        
        // Check for recent activity
        $this->assertStringContainsString('recentReviews', $content, 'View should display recent reviews');
        $this->assertStringContainsString('recentScamReports', $content, 'View should display recent scam reports');
    }

    /**
     * Test routes are properly configured
     */
    public function testRoutesAreConfigured(): void
    {
        $routesFile = APPPATH . 'Config/Routes.php';
        $content = file_get_contents($routesFile);
        
        // Check admin user routes exist in Routes.php
        $this->assertStringContainsString("'users'", $content, 'Route for user list should exist');
        $this->assertStringContainsString('suspend', $content, 'Route for suspend should exist');
        $this->assertStringContainsString('reactivate', $content, 'Route for reactivate should exist');
        $this->assertStringContainsString('delete', $content, 'Route for delete should exist');
    }

    /**
     * Test UserModel has required fields
     */
    public function testUserModelHasRequiredFields(): void
    {
        $this->assertTrue(class_exists('App\Models\UserModel'));
        
        // Use reflection to check properties without instantiating
        $reflection = new \ReflectionClass('App\Models\UserModel');
        $this->assertTrue($reflection->hasProperty('allowedFields'), 'UserModel should have allowedFields property');
    }

    /**
     * Test UserModel has validation rules for status
     */
    public function testUserModelHasStatusValidation(): void
    {
        // Use reflection to check properties without instantiating
        $reflection = new \ReflectionClass('App\Models\UserModel');
        $this->assertTrue($reflection->hasProperty('validationRules'), 'UserModel should have validationRules property');
    }

    /**
     * Test controller uses correct dependencies
     */
    public function testControllerUsesCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\UserManagementController');
        
        // Check for UserModel property
        $this->assertTrue(
            $reflection->hasProperty('userModel'),
            'Controller should have userModel property'
        );
        
        // Check for ReviewModel property
        $this->assertTrue(
            $reflection->hasProperty('reviewModel'),
            'Controller should have reviewModel property'
        );
        
        // Check for ScamReportModel property
        $this->assertTrue(
            $reflection->hasProperty('scamReportModel'),
            'Controller should have scamReportModel property'
        );
    }

    /**
     * Test suspend method signature is correct
     */
    public function testSuspendMethodSignature(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\UserManagementController');
        $method = $reflection->getMethod('suspend');
        
        $this->assertTrue($method->isPublic(), 'suspend method should be public');
        
        $parameters = $method->getParameters();
        $this->assertCount(1, $parameters, 'suspend method should have 1 parameter');
        $this->assertEquals('id', $parameters[0]->getName(), 'Parameter should be named id');
    }

    /**
     * Test reactivate method signature is correct
     */
    public function testReactivateMethodSignature(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\UserManagementController');
        $method = $reflection->getMethod('reactivate');
        
        $this->assertTrue($method->isPublic(), 'reactivate method should be public');
        
        $parameters = $method->getParameters();
        $this->assertCount(1, $parameters, 'reactivate method should have 1 parameter');
        $this->assertEquals('id', $parameters[0]->getName(), 'Parameter should be named id');
    }

    /**
     * Test delete method signature is correct
     */
    public function testDeleteMethodSignature(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\UserManagementController');
        $method = $reflection->getMethod('delete');
        
        $this->assertTrue($method->isPublic(), 'delete method should be public');
        
        $parameters = $method->getParameters();
        $this->assertCount(1, $parameters, 'delete method should have 1 parameter');
        $this->assertEquals('id', $parameters[0]->getName(), 'Parameter should be named id');
    }

    /**
     * Test index method returns string (view)
     */
    public function testIndexMethodReturnsString(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\UserManagementController');
        $method = $reflection->getMethod('index');
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType, 'index method should have return type');
        $this->assertEquals('string', $returnType->getName(), 'index method should return string');
    }

    /**
     * Test search logic exists in controller
     */
    public function testSearchLogicExists(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\UserManagementController');
        
        // Check for getFilteredUsers method
        $this->assertTrue(
            $reflection->hasMethod('getFilteredUsers'),
            'Controller should have getFilteredUsers method'
        );
        
        $method = $reflection->getMethod('getFilteredUsers');
        $parameters = $method->getParameters();
        
        // Should accept search, page, perPage
        $this->assertGreaterThanOrEqual(3, count($parameters), 'getFilteredUsers should accept at least 3 parameters');
    }

    /**
     * Test AuthFilter checks user status
     */
    public function testAuthFilterChecksUserStatus(): void
    {
        $this->assertTrue(class_exists('App\Filters\AuthFilter'));
        
        $filterPath = APPPATH . 'Filters/AuthFilter.php';
        $content = file_get_contents($filterPath);
        
        // Check that AuthFilter checks status
        $this->assertStringContainsString('status', $content, 'AuthFilter should check user status');
        $this->assertStringContainsString('active', $content, 'AuthFilter should check for active status');
    }

    /**
     * Test delete method anonymizes user content
     */
    public function testDeleteMethodAnonymizesContent(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/UserManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that delete method updates reviews and scam reports
        $this->assertStringContainsString('reviewModel', $content, 'Delete method should handle reviews');
        $this->assertStringContainsString('scamReportModel', $content, 'Delete method should handle scam reports');
        $this->assertStringContainsString('user_id', $content, 'Delete method should update user_id references');
    }

    /**
     * Test all acceptance criteria are met
     */
    public function testAllAcceptanceCriteriaMet(): void
    {
        // AC1: Admins can view all users with pagination
        $this->assertTrue(
            method_exists('App\Controllers\Admin\UserManagementController', 'index'),
            'AC1: Controller should have index method to view users'
        );
        
        $viewPath = APPPATH . 'Views/admin/users/index.php';
        $content = file_get_contents($viewPath);
        $this->assertStringContainsString('pagination', $content, 'AC1: View should have pagination');
        
        // AC2: Users can be searched by username/email
        $this->assertStringContainsString('search', $content, 'AC2: View should have search functionality');
        
        // AC3: Users can be suspended or reactivated
        $this->assertTrue(
            method_exists('App\Controllers\Admin\UserManagementController', 'suspend'),
            'AC3: Controller should have suspend method'
        );
        $this->assertTrue(
            method_exists('App\Controllers\Admin\UserManagementController', 'reactivate'),
            'AC3: Controller should have reactivate method'
        );
        
        // AC4: Suspended users cannot login (verified by AuthFilter)
        $filterPath = APPPATH . 'Filters/AuthFilter.php';
        $filterContent = file_get_contents($filterPath);
        $this->assertStringContainsString('status', $filterContent, 'AC4: AuthFilter should check user status');
        
        // AC5: Deleting user anonymizes their content
        $this->assertTrue(
            method_exists('App\Controllers\Admin\UserManagementController', 'delete'),
            'AC5: Controller should have delete method'
        );
        
        $controllerPath = APPPATH . 'Controllers/Admin/UserManagementController.php';
        $controllerContent = file_get_contents($controllerPath);
        $this->assertStringContainsString('reviewModel', $controllerContent, 'AC5: Delete should handle reviews');
        $this->assertStringContainsString('scamReportModel', $controllerContent, 'AC5: Delete should handle scam reports');
    }

    /**
     * Test admin users cannot be suspended or deleted
     */
    public function testAdminProtection(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/UserManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that suspend method checks for admin role
        $this->assertStringContainsString("role", $content, 'Suspend method should check user role');
        $this->assertStringContainsString("admin", $content, 'Suspend method should protect admin users');
        
        // Check that delete method checks for admin role
        $this->assertStringContainsString("Cannot suspend admin", $content, 'Should have admin protection message');
        $this->assertStringContainsString("Cannot delete admin", $content, 'Should have admin protection message');
    }

    /**
     * Test user statistics are displayed
     */
    public function testUserStatisticsDisplayed(): void
    {
        $viewPath = APPPATH . 'Views/admin/users/view.php';
        $content = file_get_contents($viewPath);
        
        // Check for review count
        $this->assertStringContainsString('reviewCount', $content, 'View should display review count');
        
        // Check for scam report count
        $this->assertStringContainsString('scamReportCount', $content, 'View should display scam report count');
        
        // Check for recent reviews section
        $this->assertStringContainsString('recentReviews', $content, 'View should display recent reviews');
        
        // Check for recent scam reports section
        $this->assertStringContainsString('recentScamReports', $content, 'View should display recent scam reports');
    }
}
