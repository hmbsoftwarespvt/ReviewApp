<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * AuthController
 * 
 * Handles user authentication including registration, login, and logout.
 * Implements password hashing with bcrypt cost 12, email verification token generation,
 * session management with 30-day expiration, and failed login tracking.
 */
class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    /**
     * Display registration form
     */
    public function showRegister()
    {
        return view('auth/register');
    }

    /**
     * Handle user registration
     * 
     * Accepts: email, username, password, password_confirm
     * - Validates input data
     * - Hashes password with bcrypt cost 12
     * - Generates email verification token
     * - Creates user account with status 'active' and email_verified false
     * 
     * @return RedirectResponse
     */
    public function register()
    {
        // Validation rules
        $rules = [
            'username' => [
                'label'  => 'Username',
                'rules'  => 'required|min_length[3]|max_length[50]|alpha_numeric_punct|is_unique[users.username]',
                'errors' => [
                    'required'      => 'Username is required',
                    'min_length'    => 'Username must be at least 3 characters',
                    'max_length'    => 'Username cannot exceed 50 characters',
                    'alpha_numeric_punct' => 'Username can only contain letters, numbers, and underscores',
                    'is_unique'     => 'Username is already taken',
                ]
            ],
            'email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email|max_length[255]|is_unique[users.email]',
                'errors' => [
                    'required'      => 'Email is required',
                    'valid_email'   => 'Please provide a valid email address',
                    'is_unique'     => 'Email is already registered',
                ]
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required|min_length[8]|max_length[255]',
                'errors' => [
                    'required'      => 'Password is required',
                    'min_length'    => 'Password must be at least 8 characters',
                ]
            ],
            'password_confirm' => [
                'label'  => 'Password Confirmation',
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required'      => 'Password confirmation is required',
                    'matches'       => 'Passwords do not match',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Hash password with bcrypt cost 12
        $password = $this->request->getPost('password');
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Generate email verification token
        $verificationToken = bin2hex(random_bytes(32));

        // Prepare user data
        $userData = [
            'username'           => $this->request->getPost('username'),
            'email'              => $this->request->getPost('email'),
            'password_hash'      => $passwordHash,
            'role'               => 'user',
            'status'             => 'active',
            'email_verified'     => false,
            'verification_token' => $verificationToken,
            'failed_login_count' => 0,
        ];

        // Create user account
        if ($this->userModel->insert($userData)) {
            // TODO: Send verification email with token (Task 33 - Email Notification Service)
            
            return redirect()->to('/auth/login')
                           ->with('success', 'Registration successful! Please check your email to verify your account.');
        }

        return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
    }

    /**
     * Display login form
     */
    public function showLogin()
    {
        return view('auth/login');
    }

    /**
     * Handle user login
     * 
     * Accepts: identifier (email or username), password
     * - Supports login with email or username
     * - Verifies password
     * - Checks account lock status
     * - Tracks failed login attempts (locks after 5 failures within 15 minutes)
     * - Creates session with 30-day expiration on success
     * 
     * @return RedirectResponse
     */
    public function login()
    {
        // Validation rules
        $rules = [
            'identifier' => [
                'label'  => 'Email or Username',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Email or username is required',
                ]
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Password is required',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $identifier = $this->request->getPost('identifier');
        $password = $this->request->getPost('password');

        // Find user by email or username
        $user = $this->userModel->findByEmailOrUsername($identifier);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid credentials');
        }

        // Check if account is locked
        if ($this->userModel->isAccountLocked($user['id'])) {
            return redirect()->back()->withInput()->with('error', 'Account is locked due to multiple failed login attempts. Please try again later.');
        }

        // Check if account is suspended or deleted
        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Account is not active. Please contact support.');
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            // Increment failed login count
            $this->userModel->incrementFailedLogin($user['id']);

            // Check if we need to lock the account (5 failed attempts within 15 minutes)
            $failedCount = $user['failed_login_count'] + 1;
            $lastFailedLogin = $user['last_failed_login'] ? strtotime($user['last_failed_login']) : 0;
            $fifteenMinutesAgo = strtotime('-15 minutes');

            if ($failedCount >= 5 && $lastFailedLogin > $fifteenMinutesAgo) {
                // Lock account for 30 minutes
                $this->userModel->lockAccount($user['id'], 30);
                return redirect()->back()->withInput()->with('error', 'Account locked due to multiple failed login attempts. Please try again in 30 minutes.');
            }

            return redirect()->back()->withInput()->with('error', 'Invalid credentials');
        }

        // Reset failed login count on successful login
        $this->userModel->resetFailedLogin($user['id']);

        // Update last login timestamp
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s'),
        ]);

        // Create session
        $session = session();
        
        $session->set([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'status'    => $user['status'],
            'logged_in' => true,
            'isLoggedIn'=> true,
        ]);

        // Redirect based on role
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin/dashboard')->with('success', 'Welcome back, ' . $user['username'] . '!');
        }

        return redirect()->to('/')->with('success', 'Welcome back, ' . $user['username'] . '!');
    }

    /**
     * Handle user logout
     * 
     * Terminates the authenticated session and redirects to home page
     * 
     * @return RedirectResponse
     */
    public function logout()
    {
        $session = session();
        
        // Destroy all session data
        $session->destroy();

        return redirect()->to('/')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Display forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth/forgot_password');
    }

    /**
     * Handle forgot password request
     * 
     * Accepts: email
     * - Validates email exists in database
     * - Generates reset token with 60-minute expiration
     * - Stores token and expiration in database
     * - Sends password reset email (TODO: Task 33)
     * 
     * @return RedirectResponse
     */
    public function forgotPassword()
    {
        // Validation rules
        $rules = [
            'email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email is required',
                    'valid_email' => 'Please provide a valid email address',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        
        // Find user by email
        $user = $this->userModel->where('email', $email)->first();

        // Always show success message to prevent email enumeration
        if (!$user) {
            return redirect()->to('/auth/login')
                           ->with('success', 'If an account exists with that email, a password reset link has been sent.');
        }

        // Generate reset token (64 character hex string)
        $resetToken = bin2hex(random_bytes(32));
        
        // Set expiration to 60 minutes from now
        $expiresAt = date('Y-m-d H:i:s', strtotime('+60 minutes'));

        // Update user with reset token and expiration
        $this->userModel->update($user['id'], [
            'reset_token'         => $resetToken,
            'reset_token_expires' => $expiresAt,
        ]);

        // TODO: Send password reset email with token (Task 33 - Email Notification Service)
        // Email should contain link: /auth/reset-password?token={$resetToken}

        return redirect()->to('/auth/login')
                       ->with('success', 'If an account exists with that email, a password reset link has been sent.');
    }

    /**
     * Display reset password form
     * 
     * Validates reset token before displaying form
     */
    public function showResetPassword()
    {
        $token = $this->request->getGet('token');

        if (!$token) {
            return redirect()->to('/auth/login')
                           ->with('error', 'Invalid or missing reset token.');
        }

        // Validate token exists and is not expired
        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/auth/login')
                           ->with('error', 'Invalid reset token.');
        }

        // Check if token is expired
        $expiresAt = strtotime($user['reset_token_expires']);
        if ($expiresAt < time()) {
            return redirect()->to('/auth/login')
                           ->with('error', 'Reset token has expired. Please request a new password reset.');
        }

        return view('auth/reset_password', ['token' => $token]);
    }

    /**
     * Handle password reset
     * 
     * Accepts: token, password, password_confirm
     * - Validates token is valid and not expired
     * - Validates new password
     * - Updates password hash
     * - Clears reset token
     * - Resets failed login count
     * 
     * @return RedirectResponse
     */
    public function resetPassword()
    {
        // Validation rules
        $rules = [
            'token' => [
                'label'  => 'Reset Token',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Reset token is required',
                ]
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required|min_length[8]|max_length[255]',
                'errors' => [
                    'required'   => 'Password is required',
                    'min_length' => 'Password must be at least 8 characters',
                ]
            ],
            'password_confirm' => [
                'label'  => 'Password Confirmation',
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => 'Password confirmation is required',
                    'matches'  => 'Passwords do not match',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // Find user by reset token
        $user = $this->userModel->where('reset_token', $token)->first();

        if (!$user) {
            return redirect()->to('/auth/login')
                           ->with('error', 'Invalid reset token.');
        }

        // Check if token is expired
        $expiresAt = strtotime($user['reset_token_expires']);
        if ($expiresAt < time()) {
            return redirect()->to('/auth/login')
                           ->with('error', 'Reset token has expired. Please request a new password reset.');
        }

        // Hash new password with bcrypt cost 12
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Update user password and clear reset token
        $this->userModel->update($user['id'], [
            'password_hash'       => $passwordHash,
            'reset_token'         => null,
            'reset_token_expires' => null,
            'failed_login_count'  => 0,
            'last_failed_login'   => null,
            'account_locked_until' => null,
        ]);

        return redirect()->to('/auth/login')
                       ->with('success', 'Password has been reset successfully. You can now log in with your new password.');
    }
}
