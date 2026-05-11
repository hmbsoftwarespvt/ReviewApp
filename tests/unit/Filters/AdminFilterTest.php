<?php

namespace Tests\Unit\Filters;

use App\Filters\AdminFilter;
use CodeIgniter\Config\Services;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AdminFilterTest extends CIUnitTestCase
{
    protected AdminFilter $filter;
    protected $session;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->filter = new AdminFilter();
        $this->session = Services::session();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up session
        if ($this->session) {
            $this->session->destroy();
        }
    }

    public function testAdminFilterRedirectsUnauthenticatedUsers(): void
    {
        // Create a mock request
        $request = Services::request();
        
        // Ensure session is not authenticated
        $this->session->remove('logged_in');
        
        // Call the filter
        $result = $this->filter->before($request);
        
        // Assert that a redirect response is returned
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        
        // Assert redirect is to login page
        $this->assertStringContainsString('/auth/login', $result->getHeaderLine('Location'));
    }

    public function testAdminFilterRedirectsNonAdminUsers(): void
    {
        // Create a mock request
        $request = Services::request();
        
        // Set authenticated session with regular user role
        $this->session->set([
            'logged_in' => true,
            'user_id' => 1,
            'username' => 'testuser',
            'role' => 'user',
        ]);
        
        // Call the filter
        $result = $this->filter->before($request);
        
        // Assert that a redirect response is returned
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        
        // Assert redirect is to home page (access denied)
        $this->assertStringContainsString('/', $result->getHeaderLine('Location'));
    }

    public function testAdminFilterAllowsAdminUsers(): void
    {
        // Create a mock request
        $request = Services::request();
        
        // Set authenticated session with admin role
        $this->session->set([
            'logged_in' => true,
            'user_id' => 1,
            'username' => 'adminuser',
            'role' => 'admin',
        ]);
        
        // Call the filter
        $result = $this->filter->before($request);
        
        // Assert that no redirect occurs (null or void return)
        $this->assertNull($result);
    }

    public function testAdminFilterStoresIntendedUrl(): void
    {
        // Create a mock request
        $request = Services::request();
        
        // Ensure session is not authenticated
        $this->session->remove('logged_in');
        
        // Call the filter
        $result = $this->filter->before($request);
        
        // Assert that redirect_url is stored in session
        $this->assertTrue($this->session->has('redirect_url'));
    }
}
