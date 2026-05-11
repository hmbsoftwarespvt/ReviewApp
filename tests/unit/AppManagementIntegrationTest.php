<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * AppManagementIntegrationTest
 * 
 * Integration tests for admin app management without database dependency.
 * Tests controller structure, method existence, and basic logic.
 */
class AppManagementIntegrationTest extends CIUnitTestCase
{
    /**
     * Test AppManagementController exists and has required methods
     */
    public function testControllerExists(): void
    {
        $this->assertTrue(class_exists('App\Controllers\Admin\AppManagementController'));
        
        $controller = new \App\Controllers\Admin\AppManagementController();
        
        // Verify all required methods exist
        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'create'));
        $this->assertTrue(method_exists($controller, 'store'));
        $this->assertTrue(method_exists($controller, 'edit'));
        $this->assertTrue(method_exists($controller, 'update'));
        $this->assertTrue(method_exists($controller, 'delete'));
        $this->assertTrue(method_exists($controller, 'approve'));
        $this->assertTrue(method_exists($controller, 'reject'));
    }

    /**
     * Test AppRepository has all required methods
     */
    public function testRepositoryHasRequiredMethods(): void
    {
        $this->assertTrue(class_exists('App\Repositories\AppRepository'));
        
        $repository = new \App\Repositories\AppRepository();
        
        // Verify CRUD methods
        $this->assertTrue(method_exists($repository, 'find'));
        $this->assertTrue(method_exists($repository, 'getAll'));
        $this->assertTrue(method_exists($repository, 'create'));
        $this->assertTrue(method_exists($repository, 'update'));
        $this->assertTrue(method_exists($repository, 'delete'));
        
        // Verify search and filter methods
        $this->assertTrue(method_exists($repository, 'search'));
        $this->assertTrue(method_exists($repository, 'getPending'));
        $this->assertTrue(method_exists($repository, 'getWithDetails'));
    }

    /**
     * Test ScreenshotModel has required methods
     */
    public function testScreenshotModelHasRequiredMethods(): void
    {
        $this->assertTrue(class_exists('App\Models\ScreenshotModel'));
        
        $model = new \App\Models\ScreenshotModel();
        
        $this->assertTrue(method_exists($model, 'getByApp'));
        $this->assertTrue(method_exists($model, 'getCountByApp'));
        $this->assertTrue(method_exists($model, 'deleteByApp'));
    }

    /**
     * Test CategoryModel has getAllOrdered method
     */
    public function testCategoryModelHasGetAllOrderedMethod(): void
    {
        $this->assertTrue(class_exists('App\Models\CategoryModel'));
        
        $model = new \App\Models\CategoryModel();
        
        $this->assertTrue(method_exists($model, 'getAllOrdered'));
    }

    /**
     * Test AdminFilter exists
     */
    public function testAdminFilterExists(): void
    {
        $this->assertTrue(class_exists('App\Filters\AdminFilter'));
        
        $filter = new \App\Filters\AdminFilter();
        
        $this->assertTrue(method_exists($filter, 'before'));
        $this->assertTrue(method_exists($filter, 'after'));
    }

    /**
     * Test routes are configured
     */
    public function testRoutesAreConfigured(): void
    {
        $routes = service('routes');
        $routes->loadRoutes();
        
        // Get all routes
        $collection = $routes->getRoutes();
        
        // Check if admin app routes exist
        $this->assertArrayHasKey('admin/apps', $collection);
        $this->assertArrayHasKey('admin/apps/create', $collection);
    }

    /**
     * Test view files exist
     */
    public function testViewFilesExist(): void
    {
        $viewPath = APPPATH . 'Views/admin/apps/';
        
        $this->assertFileExists($viewPath . 'index.php');
        $this->assertFileExists($viewPath . 'form.php');
    }

    /**
     * Test AppModel validation rules
     */
    public function testAppModelValidationRules(): void
    {
        $model = new \App\Models\AppModel();
        
        $rules = $model->getValidationRules();
        
        // Verify required fields have validation
        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('slug', $rules);
        $this->assertArrayHasKey('platform_type', $rules);
        $this->assertArrayHasKey('developer_name', $rules);
        
        // Verify name is required
        $this->assertStringContainsString('required', $rules['name']);
        
        // Verify slug is unique
        $this->assertStringContainsString('is_unique', $rules['slug']);
        
        // Verify platform_type has enum validation
        $this->assertStringContainsString('in_list', $rules['platform_type']);
    }

    /**
     * Test AppModel allowed fields
     */
    public function testAppModelAllowedFields(): void
    {
        $model = new \App\Models\AppModel();
        
        $allowedFields = $model->getAllowedFields();
        
        // Verify all required fields are allowed
        $this->assertContains('name', $allowedFields);
        $this->assertContains('slug', $allowedFields);
        $this->assertContains('description', $allowedFields);
        $this->assertContains('platform_type', $allowedFields);
        $this->assertContains('developer_name', $allowedFields);
        $this->assertContains('approval_status', $allowedFields);
        $this->assertContains('permissions', $allowedFields);
        $this->assertContains('has_encryption', $allowedFields);
        $this->assertContains('third_party_sdk_count', $allowedFields);
    }

    /**
     * Test ScreenshotModel validation rules
     */
    public function testScreenshotModelValidationRules(): void
    {
        $model = new \App\Models\ScreenshotModel();
        
        $rules = $model->getValidationRules();
        
        // Verify required fields
        $this->assertArrayHasKey('app_id', $rules);
        $this->assertArrayHasKey('filename', $rules);
        $this->assertArrayHasKey('file_path', $rules);
        
        // Verify app_id is required
        $this->assertStringContainsString('required', $rules['app_id']);
        
        // Verify filename is required
        $this->assertStringContainsString('required', $rules['filename']);
    }
}
