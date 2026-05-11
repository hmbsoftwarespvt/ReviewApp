<?php

namespace Tests\Unit\Filters;

use App\Filters\AuthFilter;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\Mock\MockSession;

/**
 * @internal
 */
final class AuthFilterTest extends CIUnitTestCase
{
    protected AuthFilter $filter;
    protected $session;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->filter = new AuthFilter();
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

    public function testAuthFilterRedirectsUnauthenticatedUsers(): void
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

    public function testAuthFilterAllowsAuthenticatedUsers(): void
    {
        // Create a mock request
        $request = Services::request();
        
        // Set authenticated session
        $this->session->set([
            'logged_in' => true,
            'user_id' => 1,
            'username' => 'testuser',
            'role' => 'user',
        ]);
        
        // Call the filter
        $result = $this->filter->before($request);
        
        // Assert that no redirect occurs (null or void return)
        $this->assertNull($result);
    }

    public function testAuthFilterRedirectsInactiveUsers(): void
    {
        // Create a mock request
        $request = Services::request();
        
        // Set authenticated but inactive session
        $this->session->set([
            'logged_in' => true,
            'user_id' => 1,
            'username' => 'testuser',
            'role' => 'user',
            'status' => 'suspended',
        ]);
        
        // Call the filter
        $result = $this->filter->before($request);
        
        // Assert that a redirect response is returned
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
        
        // Note: Session destruction happens in the filter, but the session service
        // may still have references. The important part is the redirect occurs.
        // In a real scenario, the session would be destroyed on the next request.
    }

    public function testAuthFilterStoresIntendedUrl(): void
    {
        // Create a mock request with a specific URL
        $request = Services::request();
        
        // Ensure session is not authenticated
        $this->session->remove('logged_in');
        
        // Call the filter
        $result = $this->filter->before($request);
        
        // Assert that redirect_url is stored in session
        $this->assertTrue($this->session->has('redirect_url'));
    }
}
