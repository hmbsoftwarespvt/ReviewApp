<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminFilter
 * 
 * Checks if authenticated user has admin role.
 * Returns 403 Forbidden for non-admin users.
 */
class AdminFilter implements FilterInterface
{
    /**
     * Check if user has admin role
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // First check if user is logged in
        if (!$session->has('logged_in') || !$session->get('logged_in')) {
            // Store the intended URL to redirect back after login
            $session->set('redirect_url', current_url());
            
            return redirect()->to('/auth/login')
                           ->with('error', 'You must be logged in to access this page.');
        }
        
        // Check if user has admin role
        $role = $session->get('role');
        if ($role !== 'admin') {
            // Return 403 Forbidden for non-admin users
            return redirect()->to('/')
                           ->with('error', 'Access denied. You do not have permission to access this page.');
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
