<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter
 * 
 * Checks if user is authenticated before allowing access to protected routes.
 * Redirects unauthenticated users to the login page.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Check if user is authenticated
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->has('logged_in') || !$session->get('logged_in')) {
            // Store the intended URL to redirect back after login
            $session->set('redirect_url', current_url());
            
            // Redirect to login page with error message
            return redirect()->to('/auth/login')
                           ->with('error', 'You must be logged in to access this page.');
        }
        
        // Check if user account is active
        $status = $session->get('status');
        if ($status && $status !== 'active') {
            // Destroy session for inactive accounts
            $session->destroy();
            
            return redirect()->to('/auth/login')
                           ->with('error', 'Your account is not active. Please contact support.');
        }
    }

    /**
     * Allows After filters to inspect and modify the response object as needed.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
