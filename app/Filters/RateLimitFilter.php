<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RateLimitFilter
 * 
 * Enforces rate limiting on API endpoints and form submissions.
 * Limits requests per IP address within a time window.
 * 
 * Default: 60 requests per minute per IP address
 */
class RateLimitFilter implements FilterInterface
{
    /**
     * Rate limit configuration
     */
    protected int $maxRequests = 60;      // Maximum requests allowed
    protected int $timeWindow = 60;       // Time window in seconds
    protected string $cachePrefix = 'rate_limit_';
    
    /**
     * Check rate limit before processing request
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get client IP address
        $ipAddress = $request->getIPAddress();
        
        // Create cache key based on IP address
        $cacheKey = $this->cachePrefix . md5($ipAddress);
        
        // Get cache instance
        $cache = \Config\Services::cache();
        
        // Get current request count from cache
        $requestData = $cache->get($cacheKey);
        
        if ($requestData === null) {
            // First request in this time window
            $requestData = [
                'count' => 1,
                'reset_time' => time() + $this->timeWindow,
            ];
            
            // Store in cache with TTL
            $cache->save($cacheKey, $requestData, $this->timeWindow);
            
            // Add rate limit headers
            $this->addRateLimitHeaders($request, 1, $this->maxRequests, $requestData['reset_time']);
            
            return;
        }
        
        // Check if time window has expired
        if (time() >= $requestData['reset_time']) {
            // Reset counter for new time window
            $requestData = [
                'count' => 1,
                'reset_time' => time() + $this->timeWindow,
            ];
            
            $cache->save($cacheKey, $requestData, $this->timeWindow);
            
            // Add rate limit headers
            $this->addRateLimitHeaders($request, 1, $this->maxRequests, $requestData['reset_time']);
            
            return;
        }
        
        // Increment request count
        $requestData['count']++;
        
        // Check if limit exceeded
        if ($requestData['count'] > $this->maxRequests) {
            // Rate limit exceeded
            $retryAfter = $requestData['reset_time'] - time();
            
            // Return 429 Too Many Requests
            return service('response')
                ->setStatusCode(429, 'Too Many Requests')
                ->setHeader('Retry-After', (string) $retryAfter)
                ->setHeader('X-RateLimit-Limit', (string) $this->maxRequests)
                ->setHeader('X-RateLimit-Remaining', '0')
                ->setHeader('X-RateLimit-Reset', (string) $requestData['reset_time'])
                ->setJSON([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Too many requests. Please try again later.',
                    'retry_after' => $retryAfter,
                ]);
        }
        
        // Update cache with new count
        $cache->save($cacheKey, $requestData, $this->timeWindow);
        
        // Add rate limit headers
        $this->addRateLimitHeaders(
            $request,
            $requestData['count'],
            $this->maxRequests,
            $requestData['reset_time']
        );
    }

    /**
     * Add rate limit headers to response
     * 
     * @param RequestInterface $request
     * @param int $currentCount
     * @param int $maxRequests
     * @param int $resetTime
     * @return void
     */
    protected function addRateLimitHeaders(
        RequestInterface $request,
        int $currentCount,
        int $maxRequests,
        int $resetTime
    ): void {
        // Store headers in request attributes for use in after() method
        $request->rateLimit = [
            'limit' => $maxRequests,
            'remaining' => max(0, $maxRequests - $currentCount),
            'reset' => $resetTime,
        ];
    }

    /**
     * Add rate limit headers to response after processing
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add rate limit headers to response if available
        if (isset($request->rateLimit)) {
            $response->setHeader('X-RateLimit-Limit', (string) $request->rateLimit['limit']);
            $response->setHeader('X-RateLimit-Remaining', (string) $request->rateLimit['remaining']);
            $response->setHeader('X-RateLimit-Reset', (string) $request->rateLimit['reset']);
        }
        
        return $response;
    }
}
