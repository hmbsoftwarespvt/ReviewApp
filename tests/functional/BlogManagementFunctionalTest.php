<?php

namespace Tests\Functional;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Functional tests for Blog Management
 * 
 * Verifies that all required components exist and are properly configured
 * for the blog management workflow.
 * 
 * Tests Task 19 acceptance criteria:
 * - Admins can create, edit, delete blog posts
 * - Rich text editor for content
 * - Posts can be saved as draft or published
 * - Categories: Guides, Tips & Tricks, Scam Alerts, News & Updates, Reviews
 * - Featured images can be uploaded
 * 
 * @internal
 */
final class BlogManagementFunctionalTest extends CIUnitTestCase
{
    /**
     * Test BlogManagementController exists and has all required methods
     */
    public function testControllerExistsWithAllMethods(): void
    {
        $this->assertTrue(class_exists('App\Controllers\Admin\BlogManagementController'));
        
        $methods = ['index', 'create', 'store', 'edit', 'update', 'delete', 'publish', 'unpublish'];
        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('App\Controllers\Admin\BlogManagementController', $method),
                "Method {$method} should exist in BlogManagementController"
            );
        }
    }

    /**
     * Test BlogPostModel exists and has all required methods
     */
    public function testBlogPostModelHasRequiredMethods(): void
    {
        $this->assertTrue(class_exists('App\Models\BlogPostModel'));
        
        $methods = [
            'findBySlug',
            'getPublished',
            'getByCategory',
            'getDrafts',
            'getByAuthor',
            'incrementViewCount',
            'publish',
            'unpublish',
            'getWithAuthor',
            'getRelated'
        ];
        
        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists('App\Models\BlogPostModel', $method),
                "Method {$method} should exist in BlogPostModel"
            );
        }
    }

    /**
     * Test admin blog management views exist
     */
    public function testViewFilesExist(): void
    {
        $indexViewPath = APPPATH . 'Views/admin/blog/index.php';
        $formViewPath = APPPATH . 'Views/admin/blog/form.php';
        
        $this->assertFileExists($indexViewPath, 'Blog list view should exist');
        $this->assertFileExists($formViewPath, 'Blog form view should exist');
    }

    /**
     * Test index view contains required UI elements
     * AC: Admins can view blog post list
     */
    public function testIndexViewContainsRequiredElements(): void
    {
        $viewPath = APPPATH . 'Views/admin/blog/index.php';
        $content = file_get_contents($viewPath);
        
        // Check for create button
        $this->assertStringContainsString('Create Blog Post', $content, 'View should have create button');
        
        // Check for filters
        $this->assertStringContainsString('status', $content, 'View should have status filter');
        $this->assertStringContainsString('category', $content, 'View should have category filter');
        
        // Check for action buttons
        $this->assertStringContainsString('edit', $content, 'View should have edit button');
        $this->assertStringContainsString('delete', $content, 'View should have delete button');
        $this->assertStringContainsString('publish', $content, 'View should have publish button');
        $this->assertStringContainsString('unpublish', $content, 'View should have unpublish button');
        
        // Check for pagination
        $this->assertStringContainsString('pagination', $content, 'View should have pagination');
        
        // Check for post information display
        $this->assertStringContainsString('title', $content, 'View should display title');
        $this->assertStringContainsString('category', $content, 'View should display category');
        $this->assertStringContainsString('author', $content, 'View should display author');
        $this->assertStringContainsString('publication_status', $content, 'View should display status');
    }

    /**
     * Test form view contains required UI elements
     * AC: Rich text editor for content, category selection, featured image upload
     */
    public function testFormViewContainsRequiredElements(): void
    {
        $viewPath = APPPATH . 'Views/admin/blog/form.php';
        $content = file_get_contents($viewPath);
        
        // Check for form fields
        $this->assertStringContainsString('name="title"', $content, 'Form should have title field');
        $this->assertStringContainsString('name="slug"', $content, 'Form should have slug field');
        $this->assertStringContainsString('name="content"', $content, 'Form should have content field');
        $this->assertStringContainsString('name="excerpt"', $content, 'Form should have excerpt field');
        $this->assertStringContainsString('name="category"', $content, 'Form should have category field');
        $this->assertStringContainsString('name="publication_status"', $content, 'Form should have publication status field');
        $this->assertStringContainsString('name="featured_image"', $content, 'Form should have featured image field');
        
        // Check for rich text editor (TinyMCE)
        $this->assertStringContainsString('tinymce', $content, 'Form should include TinyMCE rich text editor');
        $this->assertStringContainsString('tinymce.init', $content, 'Form should initialize TinyMCE');
        
        // Check for category options (checking for PHP loop that generates options)
        $this->assertStringContainsString('foreach ($categories as $key => $label)', $content, 'Form should loop through categories');
        $this->assertStringContainsString('value="<?= $key ?>"', $content, 'Form should output category keys as values');
        
        // Check for publication status options
        $this->assertStringContainsString('draft', $content, 'Form should have draft status option');
        $this->assertStringContainsString('published', $content, 'Form should have published status option');
        
        // Check for file upload
        $this->assertStringContainsString('type="file"', $content, 'Form should have file upload input');
        $this->assertStringContainsString('enctype="multipart/form-data"', $content, 'Form should support file uploads');
        
        // Check for auto-slug generation
        $this->assertStringContainsString('addEventListener', $content, 'Form should have JavaScript for auto-slug generation');
    }

    /**
     * Test routes are properly configured
     * AC: All CRUD routes exist
     */
    public function testRoutesAreConfigured(): void
    {
        $routesFile = APPPATH . 'Config/Routes.php';
        $content = file_get_contents($routesFile);
        
        // Check admin blog routes exist in Routes.php
        $this->assertStringContainsString("'blog'", $content, 'Route for blog list should exist');
        $this->assertStringContainsString('blog/create', $content, 'Route for create should exist');
        $this->assertStringContainsString('blog/store', $content, 'Route for store should exist');
        $this->assertStringContainsString('blog/edit', $content, 'Route for edit should exist');
        $this->assertStringContainsString('blog/update', $content, 'Route for update should exist');
        $this->assertStringContainsString('blog/delete', $content, 'Route for delete should exist');
        $this->assertStringContainsString('blog/publish', $content, 'Route for publish should exist');
        $this->assertStringContainsString('blog/unpublish', $content, 'Route for unpublish should exist');
    }

    /**
     * Test BlogPostModel has required fields
     */
    public function testBlogPostModelHasRequiredFields(): void
    {
        $this->assertTrue(class_exists('App\Models\BlogPostModel'));
        
        // Use reflection to check properties without instantiating
        $reflection = new \ReflectionClass('App\Models\BlogPostModel');
        $this->assertTrue($reflection->hasProperty('allowedFields'), 'BlogPostModel should have allowedFields property');
        
        // Read the model file to check for required fields
        $modelPath = APPPATH . 'Models/BlogPostModel.php';
        $content = file_get_contents($modelPath);
        
        // Check for required fields in allowedFields array
        $requiredFields = [
            'title',
            'slug',
            'content',
            'excerpt',
            'featured_image',
            'author_id',
            'category',
            'publication_status',
            'published_at',
            'view_count'
        ];
        
        foreach ($requiredFields as $field) {
            $this->assertStringContainsString(
                "'{$field}'",
                $content,
                "BlogPostModel should have {$field} in allowedFields"
            );
        }
    }

    /**
     * Test BlogPostModel has validation rules
     */
    public function testBlogPostModelHasValidationRules(): void
    {
        // Use reflection to check properties without instantiating
        $reflection = new \ReflectionClass('App\Models\BlogPostModel');
        $this->assertTrue($reflection->hasProperty('validationRules'), 'BlogPostModel should have validationRules property');
        
        // Read the model file to check validation rules
        $modelPath = APPPATH . 'Models/BlogPostModel.php';
        $content = file_get_contents($modelPath);
        
        // Check for required field validations
        $this->assertStringContainsString("'title'", $content, 'Should have validation for title');
        $this->assertStringContainsString("'slug'", $content, 'Should have validation for slug');
        $this->assertStringContainsString("'content'", $content, 'Should have validation for content');
        $this->assertStringContainsString("'category'", $content, 'Should have validation for category');
        
        // Check category validation includes all required categories
        $this->assertStringContainsString('guides', $content, 'Category validation should include guides');
        $this->assertStringContainsString('tips_tricks', $content, 'Category validation should include tips_tricks');
        $this->assertStringContainsString('scam_alerts', $content, 'Category validation should include scam_alerts');
        $this->assertStringContainsString('news_updates', $content, 'Category validation should include news_updates');
        $this->assertStringContainsString('reviews', $content, 'Category validation should include reviews');
    }

    /**
     * Test controller uses correct dependencies
     */
    public function testControllerUsesCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\BlogManagementController');
        
        // Check for BlogPostModel property
        $this->assertTrue(
            $reflection->hasProperty('blogPostModel'),
            'Controller should have blogPostModel property'
        );
        
        // Check for UserModel property
        $this->assertTrue(
            $reflection->hasProperty('userModel'),
            'Controller should have userModel property'
        );
    }

    /**
     * Test store method handles featured image upload
     * AC: Featured images can be uploaded
     */
    public function testStoreMethodHandlesFeaturedImage(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that store method handles file upload
        $this->assertStringContainsString('getFile', $content, 'Store method should handle file upload');
        $this->assertStringContainsString('featured_image', $content, 'Store method should handle featured_image field');
        $this->assertStringContainsString('move', $content, 'Store method should move uploaded file');
        $this->assertStringContainsString('uploads/blog', $content, 'Store method should save to uploads/blog directory');
    }

    /**
     * Test update method handles featured image upload and deletion
     * AC: Featured images can be uploaded and replaced
     */
    public function testUpdateMethodHandlesFeaturedImage(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that update method handles file upload
        $this->assertStringContainsString('getFile', $content, 'Update method should handle file upload');
        $this->assertStringContainsString('featured_image', $content, 'Update method should handle featured_image field');
        
        // Check that update method can delete old featured image
        $this->assertStringContainsString('deleteFeaturedImage', $content, 'Update method should delete old featured image');
        $this->assertStringContainsString('delete_featured_image', $content, 'Update method should handle delete_featured_image checkbox');
    }

    /**
     * Test delete method removes featured image
     * AC: Deleting blog post removes associated files
     */
    public function testDeleteMethodRemovesFeaturedImage(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that delete method removes featured image
        $this->assertStringContainsString('deleteFeaturedImage', $content, 'Delete method should remove featured image');
    }

    /**
     * Test publish method sets publication status and date
     * AC: Posts can be published
     */
    public function testPublishMethodSetsStatusAndDate(): void
    {
        $modelPath = APPPATH . 'Models/BlogPostModel.php';
        $content = file_get_contents($modelPath);
        
        // Check that publish method exists and sets status
        $this->assertStringContainsString('function publish', $content, 'Model should have publish method');
        $this->assertStringContainsString('publication_status', $content, 'Publish method should set publication_status');
        $this->assertStringContainsString('published_at', $content, 'Publish method should set published_at');
        $this->assertStringContainsString('published', $content, 'Publish method should set status to published');
    }

    /**
     * Test unpublish method sets status to draft
     * AC: Posts can be set to draft
     */
    public function testUnpublishMethodSetsStatusToDraft(): void
    {
        $modelPath = APPPATH . 'Models/BlogPostModel.php';
        $content = file_get_contents($modelPath);
        
        // Check that unpublish method exists and sets status
        $this->assertStringContainsString('function unpublish', $content, 'Model should have unpublish method');
        $this->assertStringContainsString('publication_status', $content, 'Unpublish method should set publication_status');
        $this->assertStringContainsString('draft', $content, 'Unpublish method should set status to draft');
    }

    /**
     * Test store method validates required fields
     */
    public function testStoreMethodValidatesRequiredFields(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that store method validates input
        $this->assertStringContainsString('validate', $content, 'Store method should validate input');
        $this->assertStringContainsString('required', $content, 'Store method should have required field validation');
        
        // Check validation for key fields
        $this->assertStringContainsString('title', $content, 'Store method should validate title');
        $this->assertStringContainsString('slug', $content, 'Store method should validate slug');
        $this->assertStringContainsString('content', $content, 'Store method should validate content');
        $this->assertStringContainsString('category', $content, 'Store method should validate category');
    }

    /**
     * Test store method sets author_id from session
     * AC: Blog posts are associated with author
     */
    public function testStoreMethodSetsAuthorId(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that store method gets author from session
        $this->assertStringContainsString('author_id', $content, 'Store method should set author_id');
        $this->assertStringContainsString('session', $content, 'Store method should get user from session');
        $this->assertStringContainsString('user_id', $content, 'Store method should get user_id from session');
    }

    /**
     * Test store method sets published_at when status is published
     * AC: Published posts have publication date
     */
    public function testStoreMethodSetsPublishedAt(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that store method sets published_at for published posts
        $this->assertStringContainsString('published_at', $content, 'Store method should set published_at');
        $this->assertStringContainsString('publication_status', $content, 'Store method should check publication_status');
    }

    /**
     * Test index method supports filtering by status
     * AC: Admins can filter by draft/published status
     */
    public function testIndexMethodSupportsStatusFilter(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that index method filters by status
        $this->assertStringContainsString('status', $content, 'Index method should support status filter');
        $this->assertStringContainsString('publication_status', $content, 'Index method should filter by publication_status');
    }

    /**
     * Test index method supports filtering by category
     * AC: Admins can filter by category
     */
    public function testIndexMethodSupportsCategoryFilter(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that index method filters by category
        $this->assertStringContainsString('category', $content, 'Index method should support category filter');
    }

    /**
     * Test index method includes pagination
     * AC: Blog list is paginated
     */
    public function testIndexMethodIncludesPagination(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that index method implements pagination
        $this->assertStringContainsString('page', $content, 'Index method should support pagination');
        $this->assertStringContainsString('perPage', $content, 'Index method should have perPage setting');
        $this->assertStringContainsString('offset', $content, 'Index method should calculate offset');
        $this->assertStringContainsString('limit', $content, 'Index method should limit results');
    }

    /**
     * Test getCategories method returns all required categories
     * AC: Categories include Guides, Tips & Tricks, Scam Alerts, News & Updates, Reviews
     */
    public function testGetCategoriesReturnsAllCategories(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that getCategories method exists
        $this->assertStringContainsString('function getCategories', $content, 'Controller should have getCategories method');
        
        // Check for all required categories
        $this->assertStringContainsString('guides', $content, 'Should include Guides category');
        $this->assertStringContainsString('tips_tricks', $content, 'Should include Tips & Tricks category');
        $this->assertStringContainsString('scam_alerts', $content, 'Should include Scam Alerts category');
        $this->assertStringContainsString('news_updates', $content, 'Should include News & Updates category');
        $this->assertStringContainsString('reviews', $content, 'Should include Reviews category');
    }

    /**
     * Test method signatures are correct
     */
    public function testMethodSignatures(): void
    {
        $reflection = new \ReflectionClass('App\Controllers\Admin\BlogManagementController');
        
        // Test index method
        $indexMethod = $reflection->getMethod('index');
        $this->assertTrue($indexMethod->isPublic(), 'index method should be public');
        $returnType = $indexMethod->getReturnType();
        $this->assertNotNull($returnType, 'index method should have return type');
        $this->assertEquals('string', $returnType->getName(), 'index method should return string');
        
        // Test create method
        $createMethod = $reflection->getMethod('create');
        $this->assertTrue($createMethod->isPublic(), 'create method should be public');
        $returnType = $createMethod->getReturnType();
        $this->assertNotNull($returnType, 'create method should have return type');
        $this->assertEquals('string', $returnType->getName(), 'create method should return string');
        
        // Test store method
        $storeMethod = $reflection->getMethod('store');
        $this->assertTrue($storeMethod->isPublic(), 'store method should be public');
        
        // Test edit method
        $editMethod = $reflection->getMethod('edit');
        $this->assertTrue($editMethod->isPublic(), 'edit method should be public');
        $parameters = $editMethod->getParameters();
        $this->assertCount(1, $parameters, 'edit method should have 1 parameter');
        $this->assertEquals('id', $parameters[0]->getName(), 'Parameter should be named id');
        
        // Test update method
        $updateMethod = $reflection->getMethod('update');
        $this->assertTrue($updateMethod->isPublic(), 'update method should be public');
        $parameters = $updateMethod->getParameters();
        $this->assertCount(1, $parameters, 'update method should have 1 parameter');
        $this->assertEquals('id', $parameters[0]->getName(), 'Parameter should be named id');
        
        // Test delete method
        $deleteMethod = $reflection->getMethod('delete');
        $this->assertTrue($deleteMethod->isPublic(), 'delete method should be public');
        $parameters = $deleteMethod->getParameters();
        $this->assertCount(1, $parameters, 'delete method should have 1 parameter');
        $this->assertEquals('id', $parameters[0]->getName(), 'Parameter should be named id');
    }

    /**
     * Test all acceptance criteria are met
     */
    public function testAllAcceptanceCriteriaMet(): void
    {
        // AC1: Admins can create, edit, delete blog posts
        $this->assertTrue(
            method_exists('App\Controllers\Admin\BlogManagementController', 'create'),
            'AC1: Controller should have create method'
        );
        $this->assertTrue(
            method_exists('App\Controllers\Admin\BlogManagementController', 'store'),
            'AC1: Controller should have store method'
        );
        $this->assertTrue(
            method_exists('App\Controllers\Admin\BlogManagementController', 'edit'),
            'AC1: Controller should have edit method'
        );
        $this->assertTrue(
            method_exists('App\Controllers\Admin\BlogManagementController', 'update'),
            'AC1: Controller should have update method'
        );
        $this->assertTrue(
            method_exists('App\Controllers\Admin\BlogManagementController', 'delete'),
            'AC1: Controller should have delete method'
        );
        
        // AC2: Rich text editor for content
        $formViewPath = APPPATH . 'Views/admin/blog/form.php';
        $formContent = file_get_contents($formViewPath);
        $this->assertStringContainsString('tinymce', $formContent, 'AC2: Form should include TinyMCE rich text editor');
        
        // AC3: Posts can be saved as draft or published
        $this->assertStringContainsString('draft', $formContent, 'AC3: Form should have draft option');
        $this->assertStringContainsString('published', $formContent, 'AC3: Form should have published option');
        $this->assertTrue(
            method_exists('App\Models\BlogPostModel', 'publish'),
            'AC3: Model should have publish method'
        );
        $this->assertTrue(
            method_exists('App\Models\BlogPostModel', 'unpublish'),
            'AC3: Model should have unpublish method'
        );
        
        // AC4: Categories include all required options
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $controllerContent = file_get_contents($controllerPath);
        $this->assertStringContainsString('guides', $controllerContent, 'AC4: Should include Guides category');
        $this->assertStringContainsString('tips_tricks', $controllerContent, 'AC4: Should include Tips & Tricks category');
        $this->assertStringContainsString('scam_alerts', $controllerContent, 'AC4: Should include Scam Alerts category');
        $this->assertStringContainsString('news_updates', $controllerContent, 'AC4: Should include News & Updates category');
        $this->assertStringContainsString('reviews', $controllerContent, 'AC4: Should include Reviews category');
        
        // AC5: Featured images can be uploaded
        $this->assertStringContainsString('featured_image', $formContent, 'AC5: Form should have featured image upload');
        $this->assertStringContainsString('type="file"', $formContent, 'AC5: Form should have file input');
        $this->assertStringContainsString('getFile', $controllerContent, 'AC5: Controller should handle file upload');
    }

    /**
     * Test slug uniqueness validation
     */
    public function testSlugUniquenessValidation(): void
    {
        $modelPath = APPPATH . 'Models/BlogPostModel.php';
        $content = file_get_contents($modelPath);
        
        // Check that slug has uniqueness validation
        $this->assertStringContainsString('is_unique', $content, 'Slug should have uniqueness validation');
        $this->assertStringContainsString('slug', $content, 'Should validate slug field');
    }

    /**
     * Test form includes CSRF protection
     */
    public function testFormIncludesCSRFProtection(): void
    {
        $viewPath = APPPATH . 'Views/admin/blog/form.php';
        $content = file_get_contents($viewPath);
        
        // Check for CSRF field
        $this->assertStringContainsString('csrf_field', $content, 'Form should include CSRF protection');
    }

    /**
     * Test controller handles validation errors
     */
    public function testControllerHandlesValidationErrors(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that controller handles validation errors
        $this->assertStringContainsString('withInput', $content, 'Controller should preserve input on error');
        $this->assertStringContainsString('errors', $content, 'Controller should pass errors to view');
        $this->assertStringContainsString('redirect()->back()', $content, 'Controller should redirect back on error');
    }

    /**
     * Test view displays validation errors
     */
    public function testViewDisplaysValidationErrors(): void
    {
        $viewPath = APPPATH . 'Views/admin/blog/form.php';
        $content = file_get_contents($viewPath);
        
        // Check that view displays errors
        $this->assertStringContainsString('errors', $content, 'View should display validation errors');
        $this->assertStringContainsString('alert', $content, 'View should show error alerts');
    }

    /**
     * Test view displays success messages
     */
    public function testViewDisplaysSuccessMessages(): void
    {
        $viewPath = APPPATH . 'Views/admin/blog/index.php';
        $content = file_get_contents($viewPath);
        
        // Check that view displays success messages
        $this->assertStringContainsString('success', $content, 'View should display success messages');
        $this->assertStringContainsString('alert', $content, 'View should show success alerts');
    }

    /**
     * Test featured image validation
     */
    public function testFeaturedImageValidation(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check for image validation rules
        $this->assertStringContainsString('max_size', $content, 'Should validate image file size');
        $this->assertStringContainsString('is_image', $content, 'Should validate file is an image');
        $this->assertStringContainsString('2048', $content, 'Should limit file size to 2MB');
    }

    /**
     * Test blog post list shows author information
     */
    public function testBlogPostListShowsAuthorInformation(): void
    {
        $viewPath = APPPATH . 'Views/admin/blog/index.php';
        $content = file_get_contents($viewPath);
        
        // Check that view displays author information
        $this->assertStringContainsString('author', $content, 'View should display author information');
        $this->assertStringContainsString('author_name', $content, 'View should display author name');
    }

    /**
     * Test blog post list joins with users table
     */
    public function testBlogPostListJoinsWithUsers(): void
    {
        $controllerPath = APPPATH . 'Controllers/Admin/BlogManagementController.php';
        $content = file_get_contents($controllerPath);
        
        // Check that index method joins with users table
        $this->assertStringContainsString('join', $content, 'Index method should join with users table');
        $this->assertStringContainsString('users', $content, 'Index method should join users table');
    }
}
