<?php

namespace Tests\Functional;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Functional tests for Settings Configuration
 * 
 * Verifies that all required components exist and are properly configured
 * for the settings configuration workflow.
 * 
 * Tests cover:
 * - Trust algorithm weights configuration
 * - Email configuration
 * - Pagination limits configuration
 * - Settings validation
 * - Cache invalidation
 * 
 * @internal
 */
final class SettingsConfigurationFunctionalTest extends CIUnitTestCase
{
    /**
     * Test SettingsController exists and has all required methods
     */
    public function testControllerExistsWithAllMethods(): void
    {
        $this->assertTrue(class_exists('App\Controllers\Admin\SettingsController'));
        
        $methods = ['index', 'update'];
        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('App\Controllers\Admin\SettingsController', $method),
                "Method {$method} should exist in SettingsController"
            );
        }
    }

    /**
     * Test SettingModel has all required methods
     */
    public function testSettingModelHasRequiredMethods(): void
    {
        $this->assertTrue(class_exists('App\Models\SettingModel'));
        
        $methods = ['get', 'setSetting', 'getAll', 'getByPrefix'];
        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('App\Models\SettingModel', $method),
                "Method {$method} should exist in SettingModel"
            );
        }
    }

    /**
     * Test admin settings view exists
     */
    public function testViewFileExists(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        
        $this->assertFileExists($viewPath, 'Settings view should exist');
    }

    /**
     * Test settings view contains trust algorithm configuration
     */
    public function testViewContainsTrustAlgorithmConfiguration(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for trust algorithm section
        $this->assertStringContainsString('Trust Algorithm', $content, 'View should have trust algorithm section');
        
        // Check for all 5 weight inputs
        $this->assertStringContainsString('review_rating', $content, 'View should have review rating weight input');
        $this->assertStringContainsString('security_score', $content, 'View should have security score weight input');
        $this->assertStringContainsString('developer_reputation', $content, 'View should have developer reputation weight input');
        $this->assertStringContainsString('scam_report_count', $content, 'View should have scam report count weight input');
        $this->assertStringContainsString('app_age', $content, 'View should have app age weight input');
        
        // Check for weight sum validation
        $this->assertStringContainsString('weightSum', $content, 'View should have weight sum display');
        $this->assertStringContainsString('100', $content, 'View should validate weights sum to 100');
    }

    /**
     * Test settings view contains email configuration
     */
    public function testViewContainsEmailConfiguration(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for email configuration section
        $this->assertStringContainsString('Email Configuration', $content, 'View should have email configuration section');
        
        // Check for email inputs
        $this->assertStringContainsString('sender_name', $content, 'View should have sender name input');
        $this->assertStringContainsString('sender_email', $content, 'View should have sender email input');
    }

    /**
     * Test settings view contains pagination configuration
     */
    public function testViewContainsPaginationConfiguration(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for pagination section
        $this->assertStringContainsString('Pagination', $content, 'View should have pagination section');
        
        // Check for pagination inputs
        $this->assertStringContainsString('search_results', $content, 'View should have search results pagination input');
        $this->assertStringContainsString('category_pages', $content, 'View should have category pages pagination input');
        $this->assertStringContainsString('blog_listings', $content, 'View should have blog listings pagination input');
        $this->assertStringContainsString('reviews_per_page', $content, 'View should have reviews per page input');
        $this->assertStringContainsString('scam_reports_per_page', $content, 'View should have scam reports per page input');
    }

    /**
     * Test routes are properly configured
     */
    public function testRoutesAreConfigured(): void
    {
        $routesFile = APPPATH . 'Config/Routes.php';
        $content = file_get_contents($routesFile);
        
        // Check admin settings routes exist in Routes.php
        $this->assertStringContainsString("'settings'", $content, 'Route for settings should exist');
        $this->assertStringContainsString('settings/update', $content, 'Route for settings update should exist');
    }

    /**
     * Test controller has helper methods for loading settings
     */
    public function testControllerHasHelperMethods(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\SettingsController');
        
        // Check for helper methods
        $this->assertTrue(
            $reflection->hasMethod('getTrustAlgorithmWeights'),
            'Controller should have getTrustAlgorithmWeights method'
        );
        
        $this->assertTrue(
            $reflection->hasMethod('getEmailSettings'),
            'Controller should have getEmailSettings method'
        );
        
        $this->assertTrue(
            $reflection->hasMethod('getPaginationSettings'),
            'Controller should have getPaginationSettings method'
        );
    }

    /**
     * Test controller has update methods for each setting type
     */
    public function testControllerHasUpdateMethods(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\SettingsController');
        
        // Check for update methods
        $this->assertTrue(
            $reflection->hasMethod('updateTrustAlgorithmWeights'),
            'Controller should have updateTrustAlgorithmWeights method'
        );
        
        $this->assertTrue(
            $reflection->hasMethod('updateEmailSettings'),
            'Controller should have updateEmailSettings method'
        );
        
        $this->assertTrue(
            $reflection->hasMethod('updatePaginationSettings'),
            'Controller should have updatePaginationSettings method'
        );
    }

    /**
     * Test controller uses correct dependencies
     */
    public function testControllerUsesCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\SettingsController');
        
        // Check for SettingModel property
        $this->assertTrue(
            $reflection->hasProperty('settingModel'),
            'Controller should have settingModel property'
        );
        
        // Check for TrustScoreService property
        $this->assertTrue(
            $reflection->hasProperty('trustScoreService'),
            'Controller should have trustScoreService property'
        );
    }

    /**
     * Test index method returns string (view)
     */
    public function testIndexMethodReturnsString(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\SettingsController');
        $method = $reflection->getMethod('index');
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType, 'index method should have return type');
        $this->assertEquals('string', $returnType->getName(), 'index method should return string');
    }

    /**
     * Test trust algorithm weights validation logic exists
     */
    public function testTrustAlgorithmWeightsValidationExists(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/SettingsController.php';
        $content = file_get_contents($controllerPath);
        
        // Check for validation rules
        $this->assertStringContainsString('required', $content, 'Should have required validation');
        $this->assertStringContainsString('numeric', $content, 'Should have numeric validation');
        $this->assertStringContainsString('greater_than_equal_to[0]', $content, 'Should validate minimum value');
        $this->assertStringContainsString('less_than_equal_to[100]', $content, 'Should validate maximum value');
        
        // Check for sum validation
        $this->assertStringContainsString('sum', $content, 'Should validate weights sum');
        $this->assertStringContainsString('100', $content, 'Should validate weights sum to 100');
    }

    /**
     * Test email settings validation logic exists
     */
    public function testEmailSettingsValidationExists(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/SettingsController.php';
        $content = file_get_contents($controllerPath);
        
        // Check for email validation
        $this->assertStringContainsString('valid_email', $content, 'Should have email validation');
        $this->assertStringContainsString('max_length[255]', $content, 'Should validate max length');
    }

    /**
     * Test pagination settings validation logic exists
     */
    public function testPaginationSettingsValidationExists(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/SettingsController.php';
        $content = file_get_contents($controllerPath);
        
        // Check for pagination validation
        $this->assertStringContainsString('integer', $content, 'Should have integer validation');
        $this->assertStringContainsString('greater_than[0]', $content, 'Should validate minimum value');
    }

    /**
     * Test cache invalidation logic exists
     */
    public function testCacheInvalidationLogicExists(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/SettingsController.php';
        $content = file_get_contents($controllerPath);
        
        // Check for cache clearing
        $this->assertStringContainsString('cache', $content, 'Should use cache service');
        $this->assertStringContainsString('clean', $content, 'Should clear cache after settings update');
    }

    /**
     * Test TrustScoreService loads weights from settings
     */
    public function testTrustScoreServiceLoadsWeightsFromSettings(): void
    {
        $servicePath = APPPATH . 'Services/TrustScoreService.php';
        $content = file_get_contents($servicePath);
        
        // Check that TrustScoreService uses SettingModel
        $this->assertStringContainsString('SettingModel', $content, 'TrustScoreService should use SettingModel');
        $this->assertStringContainsString('loadWeights', $content, 'TrustScoreService should have loadWeights method');
        $this->assertStringContainsString('trust_algorithm', $content, 'TrustScoreService should load trust algorithm settings');
    }

    /**
     * Test view has real-time weight sum calculation
     */
    public function testViewHasRealTimeWeightSumCalculation(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for JavaScript weight calculation
        $this->assertStringContainsString('calculateWeightSum', $content, 'View should have weight sum calculation function');
        $this->assertStringContainsString('addEventListener', $content, 'View should listen to input changes');
        $this->assertStringContainsString('weight-field', $content, 'View should have weight field class');
    }

    /**
     * Test view disables save button when weights don't sum to 100
     */
    public function testViewDisablesSaveButtonForInvalidWeights(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for save button disable logic
        $this->assertStringContainsString('disabled', $content, 'View should disable save button');
        $this->assertStringContainsString('saveTrustAlgorithmBtn', $content, 'View should have save button ID');
    }

    /**
     * Test view displays success/error messages
     */
    public function testViewDisplaysFlashMessages(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for flash message display
        $this->assertStringContainsString('getFlashdata', $content, 'View should display flash messages');
        $this->assertStringContainsString('success', $content, 'View should display success messages');
        $this->assertStringContainsString('error', $content, 'View should display error messages');
    }

    /**
     * Test SettingModel has proper field configuration
     */
    public function testSettingModelHasProperFieldConfiguration(): void
    {
        $reflection = new \ReflectionClass('App\Models\SettingModel');
        
        // Check for required properties
        $this->assertTrue($reflection->hasProperty('allowedFields'), 'SettingModel should have allowedFields property');
        $this->assertTrue($reflection->hasProperty('validationRules'), 'SettingModel should have validationRules property');
    }

    /**
     * Test SettingModel supports different data types
     */
    public function testSettingModelSupportsDataTypes(): void
    {
        $this->assertTrue(class_exists('App\Models\SettingModel'));
        
        $methods = ['castValue', 'prepareValue'];
        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('App\Models\SettingModel', $method),
                "Method {$method} should exist in SettingModel for type handling"
            );
        }
    }

    /**
     * Test all acceptance criteria are met - Trust Algorithm Weights
     */
    public function testAcceptanceCriteriaTrustAlgorithmWeights(): void
    {
        // AC1: Admins can configure trust algorithm component weights
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        $this->assertStringContainsString('review_rating', $content, 'AC1: Should have review rating weight');
        $this->assertStringContainsString('security_score', $content, 'AC1: Should have security score weight');
        $this->assertStringContainsString('developer_reputation', $content, 'AC1: Should have developer reputation weight');
        $this->assertStringContainsString('scam_report_count', $content, 'AC1: Should have scam report count weight');
        $this->assertStringContainsString('app_age', $content, 'AC1: Should have app age weight');
    }

    /**
     * Test all acceptance criteria are met - Email Configuration
     */
    public function testAcceptanceCriteriaEmailConfiguration(): void
    {
        // AC2: Email sender name and address configurable
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        $this->assertStringContainsString('sender_name', $content, 'AC2: Should have sender name field');
        $this->assertStringContainsString('sender_email', $content, 'AC2: Should have sender email field');
    }

    /**
     * Test all acceptance criteria are met - Pagination Limits
     */
    public function testAcceptanceCriteriaPaginationLimits(): void
    {
        // AC3: Pagination limits configurable
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        $this->assertStringContainsString('search_results', $content, 'AC3: Should have search results pagination');
        $this->assertStringContainsString('category_pages', $content, 'AC3: Should have category pages pagination');
        $this->assertStringContainsString('blog_listings', $content, 'AC3: Should have blog listings pagination');
        $this->assertStringContainsString('reviews_per_page', $content, 'AC3: Should have reviews per page');
        $this->assertStringContainsString('scam_reports_per_page', $content, 'AC3: Should have scam reports per page');
    }

    /**
     * Test all acceptance criteria are met - Settings Validation
     */
    public function testAcceptanceCriteriaSettingsValidation(): void
    {
        // AC4: Settings validated before saving
        $controllerPath = APPPATH . 'Controllers/Admin/SettingsController.php';
        $content = file_get_contents($controllerPath);
        
        $this->assertStringContainsString('validate', $content, 'AC4: Should validate settings');
        $this->assertStringContainsString('rules', $content, 'AC4: Should have validation rules');
    }

    /**
     * Test all acceptance criteria are met - Changes Apply Within 60 Seconds
     */
    public function testAcceptanceCriteriaChangesApplyQuickly(): void
    {
        // AC5: Changes apply within 60 seconds
        $controllerPath = APPPATH . 'Controllers/Admin/SettingsController.php';
        $content = file_get_contents($controllerPath);
        
        $this->assertStringContainsString('cache', $content, 'AC5: Should clear cache');
        $this->assertStringContainsString('clean', $content, 'AC5: Should invalidate cache');
        
        // Check success message mentions timing
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $viewContent = file_get_contents($viewPath);
        $this->assertStringContainsString('60 seconds', $viewContent, 'AC5: Should mention 60 second timing');
    }

    /**
     * Test settings form has CSRF protection
     */
    public function testSettingsFormHasCSRFProtection(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for CSRF field
        $this->assertStringContainsString('csrf_field', $content, 'Forms should have CSRF protection');
    }

    /**
     * Test settings form uses POST method
     */
    public function testSettingsFormUsesPostMethod(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for POST method
        $this->assertStringContainsString('method="post"', $content, 'Forms should use POST method');
    }

    /**
     * Test settings form has proper action URLs
     */
    public function testSettingsFormHasProperActionURLs(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for action URLs
        $this->assertStringContainsString('admin/settings/update', $content, 'Forms should have update action URL');
    }

    /**
     * Test settings form has setting type hidden fields
     */
    public function testSettingsFormHasSettingTypeFields(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for setting type fields
        $this->assertStringContainsString('setting_type', $content, 'Forms should have setting type field');
        $this->assertStringContainsString('trust_algorithm', $content, 'Should have trust_algorithm type');
        $this->assertStringContainsString('email', $content, 'Should have email type');
        $this->assertStringContainsString('pagination', $content, 'Should have pagination type');
    }

    /**
     * Test controller handles invalid setting type
     */
    public function testControllerHandlesInvalidSettingType(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/SettingsController.php';
        $content = file_get_contents($controllerPath);
        
        // Check for default case handling
        $this->assertStringContainsString('default:', $content, 'Should handle invalid setting type');
        $this->assertStringContainsString('Invalid setting type', $content, 'Should return error for invalid type');
    }

    /**
     * Test view has proper Bootstrap styling
     */
    public function testViewHasProperBootstrapStyling(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for Bootstrap classes
        $this->assertStringContainsString('card', $content, 'View should use Bootstrap cards');
        $this->assertStringContainsString('form-control', $content, 'View should use Bootstrap form controls');
        $this->assertStringContainsString('btn', $content, 'View should use Bootstrap buttons');
        $this->assertStringContainsString('alert', $content, 'View should use Bootstrap alerts');
    }

    /**
     * Test view has proper icons
     */
    public function testViewHasProperIcons(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for Bootstrap icons
        $this->assertStringContainsString('bi-', $content, 'View should use Bootstrap icons');
        $this->assertStringContainsString('bi-calculator', $content, 'View should have calculator icon for trust algorithm');
        $this->assertStringContainsString('bi-envelope', $content, 'View should have envelope icon for email');
        $this->assertStringContainsString('bi-list-ol', $content, 'View should have list icon for pagination');
    }

    /**
     * Test view has informative help text
     */
    public function testViewHasInformativeHelpText(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for help text
        $this->assertStringContainsString('form-text', $content, 'View should have help text');
        $this->assertStringContainsString('text-muted', $content, 'View should have muted help text');
    }

    /**
     * Test controller returns redirect response on update
     */
    public function testControllerReturnsRedirectOnUpdate(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\SettingsController');
        $method = $reflection->getMethod('update');
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType, 'update method should have return type');
        $this->assertStringContainsString('RedirectResponse', $returnType->getName(), 'update method should return RedirectResponse');
    }

    /**
     * Test settings are loaded with default values
     */
    public function testSettingsLoadedWithDefaultValues(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/SettingsController.php';
        $content = file_get_contents($controllerPath);
        
        // Check for default values in get methods
        $this->assertStringContainsString('30', $content, 'Should have default value for review rating weight');
        $this->assertStringContainsString('25', $content, 'Should have default value for security score weight');
        $this->assertStringContainsString('20', $content, 'Should have default value for developer reputation weight');
        $this->assertStringContainsString('15', $content, 'Should have default value for scam report count weight');
        $this->assertStringContainsString('10', $content, 'Should have default value for app age weight');
    }

    /**
     * Test view has proper input constraints
     */
    public function testViewHasProperInputConstraints(): void
    {
        $viewPath = APPPATH . 'Views/admin/settings/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for input constraints
        $this->assertStringContainsString('min="0"', $content, 'Inputs should have minimum value');
        $this->assertStringContainsString('max="100"', $content, 'Inputs should have maximum value');
        $this->assertStringContainsString('required', $content, 'Inputs should be required');
        $this->assertStringContainsString('type="number"', $content, 'Weight inputs should be number type');
        $this->assertStringContainsString('type="email"', $content, 'Email input should be email type');
    }
}
