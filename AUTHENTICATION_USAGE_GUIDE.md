# Authentication System Usage Guide

## Quick Start

The AppTrust Platform now has a complete authentication system with user registration, login, and logout functionality.

---

## User Registration

### URL
```
GET /auth/register
```

### Form Fields
- **Username**: 3-50 characters, alphanumeric with underscores
- **Email**: Valid email address, must be unique
- **Password**: Minimum 8 characters
- **Password Confirmation**: Must match password

### Example Usage
```html
<a href="<?= base_url('auth/register') ?>" class="btn btn-primary">
    Sign Up
</a>
```

### After Registration
- User account created with status 'active'
- Email verification token generated (email sending in Task 33)
- Redirected to login page with success message

---

## User Login

### URL
```
GET /auth/login
```

### Form Fields
- **Email or Username**: Accepts either email address or username
- **Password**: User's password

### Example Usage
```html
<a href="<?= base_url('auth/login') ?>" class="btn btn-outline-primary">
    Login
</a>
```

### After Login
- Session created with 30-day expiration
- Session contains: user_id, username, email, role, logged_in flag
- Admin users redirected to `/admin/dashboard`
- Regular users redirected to `/` (home page)

---

## User Logout

### URL
```
GET /auth/logout
```

### Example Usage
```html
<a href="<?= base_url('auth/logout') ?>" class="btn btn-danger">
    Logout
</a>
```

### After Logout
- All session data destroyed
- Redirected to home page with success message

---

## Checking Authentication Status

### In Controllers
```php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MyController extends BaseController
{
    public function protectedMethod()
    {
        $session = session();
        
        // Check if user is logged in
        if (!$session->has('logged_in') || !$session->get('logged_in')) {
            return redirect()->to('/auth/login')
                           ->with('error', 'Please login to continue');
        }
        
        // Get user data
        $userId = $session->get('user_id');
        $username = $session->get('username');
        $email = $session->get('email');
        $role = $session->get('role');
        
        // Your protected logic here
    }
    
    public function adminOnlyMethod()
    {
        $session = session();
        
        // Check if user is admin
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/')
                           ->with('error', 'Access denied');
        }
        
        // Your admin logic here
    }
}
```

### In Views
```php
<?php $session = session(); ?>

<?php if ($session->has('logged_in') && $session->get('logged_in')): ?>
    <!-- Logged in content -->
    <p>Welcome, <?= esc($session->get('username')) ?>!</p>
    <a href="<?= base_url('auth/logout') ?>">Logout</a>
<?php else: ?>
    <!-- Guest content -->
    <a href="<?= base_url('auth/login') ?>">Login</a>
    <a href="<?= base_url('auth/register') ?>">Register</a>
<?php endif; ?>
```

---

## Security Features

### Password Hashing
- **Algorithm**: bcrypt
- **Cost Factor**: 12
- Passwords are never stored in plain text
- Verification uses constant-time comparison

### Account Lockout
- **Trigger**: 5 failed login attempts within 15 minutes
- **Duration**: 30 minutes
- **Auto-unlock**: Account automatically unlocks after timeout
- **Message**: "Account locked due to multiple failed login attempts. Please try again in 30 minutes."

### Session Security
- **Expiration**: 30 days (2,592,000 seconds)
- **Auto-cleanup**: Expired sessions automatically removed
- **No sensitive data**: Passwords never stored in session

### CSRF Protection
- All forms include CSRF token
- CodeIgniter 4 validates tokens automatically
- Invalid tokens result in 403 Forbidden

---

## Session Data Structure

```php
[
    'user_id'   => 1,              // int: User ID from database
    'username'  => 'johndoe',      // string: Username
    'email'     => 'john@example.com', // string: Email address
    'role'      => 'user',         // string: 'user' or 'admin'
    'logged_in' => true,           // bool: Authentication flag
]
```

---

## Error Messages

### Registration Errors
- "Username is required"
- "Username must be at least 3 characters"
- "Username is already taken"
- "Email is required"
- "Please provide a valid email address"
- "Email is already registered"
- "Password is required"
- "Password must be at least 8 characters"
- "Passwords do not match"

### Login Errors
- "Email or username is required"
- "Password is required"
- "Invalid credentials"
- "Account is locked due to multiple failed login attempts. Please try again later."
- "Account is not active. Please contact support."

---

## Success Messages

### Registration
- "Registration successful! Please check your email to verify your account."

### Login
- "Welcome back, [username]!"

### Logout
- "You have been logged out successfully."

---

## Database Fields

### users Table
```sql
id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
username              VARCHAR(50) NOT NULL UNIQUE
email                 VARCHAR(255) NOT NULL UNIQUE
password_hash         VARCHAR(255) NOT NULL
role                  ENUM('user', 'admin') DEFAULT 'user'
status                ENUM('active', 'suspended', 'deleted') DEFAULT 'active'
email_verified        BOOLEAN DEFAULT FALSE
verification_token    VARCHAR(100)
reset_token           VARCHAR(100)
reset_token_expires   DATETIME
failed_login_count    INT DEFAULT 0
last_failed_login     DATETIME
account_locked_until  DATETIME
last_login            DATETIME
created_at            DATETIME DEFAULT CURRENT_TIMESTAMP
updated_at            DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

---

## UserModel Helper Methods

### `findByEmailOrUsername(string $identifier): ?array`
Finds user by email OR username.

```php
$userModel = new \App\Models\UserModel();
$user = $userModel->findByEmailOrUsername('john@example.com');
// or
$user = $userModel->findByEmailOrUsername('johndoe');
```

### `isAccountLocked(int $userId): bool`
Checks if account is currently locked.

```php
$userModel = new \App\Models\UserModel();
if ($userModel->isAccountLocked($userId)) {
    // Account is locked
}
```

### `incrementFailedLogin(int $userId): bool`
Increments failed login count and updates timestamp.

```php
$userModel = new \App\Models\UserModel();
$userModel->incrementFailedLogin($userId);
```

### `resetFailedLogin(int $userId): bool`
Resets failed login count to 0.

```php
$userModel = new \App\Models\UserModel();
$userModel->resetFailedLogin($userId);
```

### `lockAccount(int $userId, int $minutes = 30): bool`
Locks account for specified minutes.

```php
$userModel = new \App\Models\UserModel();
$userModel->lockAccount($userId, 30); // Lock for 30 minutes
```

---

## Creating Auth Filters (Task 13)

### AuthFilter Example
```php
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->has('logged_in') || !$session->get('logged_in')) {
            return redirect()->to('/auth/login')
                           ->with('error', 'Please login to continue');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
```

### AdminFilter Example
```php
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->has('logged_in') || !$session->get('logged_in')) {
            return redirect()->to('/auth/login')
                           ->with('error', 'Please login to continue');
        }
        
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/')
                           ->with('error', 'Access denied. Admin privileges required.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
```

---

## Navigation Menu Example

### Public Navigation
```php
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/') ?>">AppTrust</a>
        
        <div class="navbar-nav ms-auto">
            <?php $session = session(); ?>
            
            <?php if ($session->has('logged_in') && $session->get('logged_in')): ?>
                <!-- Logged in menu -->
                <span class="navbar-text me-3">
                    Welcome, <?= esc($session->get('username')) ?>
                </span>
                
                <?php if ($session->get('role') === 'admin'): ?>
                    <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
                        Admin Dashboard
                    </a>
                <?php endif; ?>
                
                <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                    Logout
                </a>
            <?php else: ?>
                <!-- Guest menu -->
                <a class="nav-link" href="<?= base_url('auth/login') ?>">
                    Login
                </a>
                <a class="nav-link" href="<?= base_url('auth/register') ?>">
                    Register
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
```

---

## Testing Checklist

### Manual Testing Steps

1. **Registration**
   - [ ] Visit `/auth/register`
   - [ ] Fill in all fields with valid data
   - [ ] Submit form
   - [ ] Verify redirect to login page
   - [ ] Check database for new user
   - [ ] Verify password is hashed
   - [ ] Try registering with same email (should fail)
   - [ ] Try registering with same username (should fail)

2. **Login**
   - [ ] Visit `/auth/login`
   - [ ] Login with email
   - [ ] Verify redirect to home page
   - [ ] Check session data
   - [ ] Logout
   - [ ] Login with username
   - [ ] Verify redirect to home page

3. **Failed Login**
   - [ ] Try logging in with wrong password
   - [ ] Verify error message
   - [ ] Check failed_login_count in database
   - [ ] Try 5 times
   - [ ] Verify account locked message
   - [ ] Wait 30 minutes or manually unlock
   - [ ] Login successfully

4. **Logout**
   - [ ] Login first
   - [ ] Click logout
   - [ ] Verify redirect to home page
   - [ ] Verify session destroyed
   - [ ] Try accessing protected pages

---

## Troubleshooting

### "Invalid credentials" on correct password
- Check if account is locked
- Check if account status is 'active'
- Verify password was hashed correctly during registration

### Session not persisting
- Check session configuration in `app/Config/App.php`
- Verify session driver is working (file, database, redis)
- Check browser cookies are enabled

### CSRF token mismatch
- Ensure `<?= csrf_field() ?>` is in all forms
- Check CSRF configuration in `app/Config/Security.php`
- Verify form is submitted to correct URL

### Account locked unexpectedly
- Check `account_locked_until` field in database
- Verify lockout logic (5 attempts within 15 minutes)
- Manually unlock by setting `account_locked_until` to NULL

---

## Future Enhancements (Other Tasks)

- **Task 12**: Password reset functionality
- **Task 13**: Auth and Admin filters for route protection
- **Task 33**: Email verification and notifications
- **Social Login**: OAuth integration (Google, Facebook, etc.)
- **Two-Factor Authentication**: SMS or app-based 2FA
- **Remember Me**: Persistent login with secure tokens

---

## Support

For issues or questions about the authentication system:
1. Check this guide first
2. Review the implementation summary: `TASK_11_AUTHENTICATION_SUMMARY.md`
3. Check CodeIgniter 4 documentation: https://codeigniter.com/user_guide/
4. Review the test suite: `tests/unit/AuthControllerTest.php`

---

**Last Updated**: 2025-01-XX
**Version**: 1.0.0
**CodeIgniter**: 4.5+
**PHP**: 8.2+
