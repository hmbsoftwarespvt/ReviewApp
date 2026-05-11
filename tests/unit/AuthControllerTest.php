<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\UserModel;

/**
 * AuthController Test
 * 
 * Tests user registration, login, and logout functionality
 */
class AuthControllerTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected UserModel $userModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new UserModel();
    }

    /**
     * Test registration form display
     */
    public function testShowRegisterDisplaysForm()
    {
        $result = $this->get('auth/register');
        
        $result->assertStatus(200);
        $result->assertSee('Create Account');
        $result->assertSee('Username');
        $result->assertSee('Email Address');
        $result->assertSee('Password');
    }

    /**
     * Test successful user registration
     */
    public function testRegisterCreatesUserWithHashedPassword()
    {
        $userData = [
            'username'         => 'testuser',
            'email'            => 'test@example.com',
            'password'         => 'Password123',
            'password_confirm' => 'Password123',
        ];

        $result = $this->post('auth/register', $userData);
        
        // Should redirect to login
        $result->assertRedirectTo('/auth/login');
        
        // Check user was created
        $user = $this->userModel->where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('testuser', $user['username']);
        $this->assertEquals('test@example.com', $user['email']);
        $this->assertEquals('user', $user['role']);
        $this->assertEquals('active', $user['status']);
        $this->assertFalse($user['email_verified']);
        $this->assertNotNull($user['verification_token']);
        
        // Verify password is hashed with bcrypt
        $this->assertTrue(password_verify('Password123', $user['password_hash']));
        
        // Verify bcrypt cost is 12
        $passwordInfo = password_get_info($user['password_hash']);
        $this->assertEquals('bcrypt', $passwordInfo['algoName']);
        $this->assertEquals(12, $passwordInfo['options']['cost']);
    }

    /**
     * Test registration with duplicate email
     */
    public function testRegisterRejectsDuplicateEmail()
    {
        // Create existing user
        $this->userModel->insert([
            'username'      => 'existing',
            'email'         => 'existing@example.com',
            'password_hash' => password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => 'user',
            'status'        => 'active',
        ]);

        $userData = [
            'username'         => 'newuser',
            'email'            => 'existing@example.com',
            'password'         => 'Password123',
            'password_confirm' => 'Password123',
        ];

        $result = $this->post('auth/register', $userData);
        
        // Should redirect back with error
        $result->assertRedirect();
        $result->assertSessionHas('errors');
    }

    /**
     * Test registration with duplicate username
     */
    public function testRegisterRejectsDuplicateUsername()
    {
        // Create existing user
        $this->userModel->insert([
            'username'      => 'existinguser',
            'email'         => 'existing@example.com',
            'password_hash' => password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => 'user',
            'status'        => 'active',
        ]);

        $userData = [
            'username'         => 'existinguser',
            'email'            => 'new@example.com',
            'password'         => 'Password123',
            'password_confirm' => 'Password123',
        ];

        $result = $this->post('auth/register', $userData);
        
        // Should redirect back with error
        $result->assertRedirect();
        $result->assertSessionHas('errors');
    }

    /**
     * Test registration with password mismatch
     */
    public function testRegisterRejectsPasswordMismatch()
    {
        $userData = [
            'username'         => 'testuser',
            'email'            => 'test@example.com',
            'password'         => 'Password123',
            'password_confirm' => 'DifferentPassword',
        ];

        $result = $this->post('auth/register', $userData);
        
        // Should redirect back with error
        $result->assertRedirect();
        $result->assertSessionHas('errors');
    }

    /**
     * Test login form display
     */
    public function testShowLoginDisplaysForm()
    {
        $result = $this->get('auth/login');
        
        $result->assertStatus(200);
        $result->assertSee('Welcome Back');
        $result->assertSee('Email or Username');
        $result->assertSee('Password');
    }

    /**
     * Test successful login with email
     */
    public function testLoginWithEmailCreatesSession()
    {
        // Create user
        $userId = $this->userModel->insert([
            'username'      => 'testuser',
            'email'         => 'test@example.com',
            'password_hash' => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => 'user',
            'status'        => 'active',
        ]);

        $loginData = [
            'identifier' => 'test@example.com',
            'password'   => 'Password123',
        ];

        $result = $this->post('auth/login', $loginData);
        
        // Should redirect to home
        $result->assertRedirectTo('/');
        
        // Check session was created
        $session = session();
        $this->assertTrue($session->has('logged_in'));
        $this->assertEquals($userId, $session->get('user_id'));
        $this->assertEquals('testuser', $session->get('username'));
        $this->assertEquals('test@example.com', $session->get('email'));
        $this->assertEquals('user', $session->get('role'));
    }

    /**
     * Test successful login with username
     */
    public function testLoginWithUsernameCreatesSession()
    {
        // Create user
        $userId = $this->userModel->insert([
            'username'      => 'testuser',
            'email'         => 'test@example.com',
            'password_hash' => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => 'user',
            'status'        => 'active',
        ]);

        $loginData = [
            'identifier' => 'testuser',
            'password'   => 'Password123',
        ];

        $result = $this->post('auth/login', $loginData);
        
        // Should redirect to home
        $result->assertRedirectTo('/');
        
        // Check session was created
        $session = session();
        $this->assertTrue($session->has('logged_in'));
        $this->assertEquals($userId, $session->get('user_id'));
    }

    /**
     * Test login with invalid credentials
     */
    public function testLoginWithInvalidCredentialsIncrementsFailedCount()
    {
        // Create user
        $userId = $this->userModel->insert([
            'username'      => 'testuser',
            'email'         => 'test@example.com',
            'password_hash' => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => 'user',
            'status'        => 'active',
            'failed_login_count' => 0,
        ]);

        $loginData = [
            'identifier' => 'test@example.com',
            'password'   => 'WrongPassword',
        ];

        $result = $this->post('auth/login', $loginData);
        
        // Should redirect back with error
        $result->assertRedirect();
        $result->assertSessionHas('error');
        
        // Check failed login count was incremented
        $user = $this->userModel->find($userId);
        $this->assertEquals(1, $user['failed_login_count']);
        $this->assertNotNull($user['last_failed_login']);
    }

    /**
     * Test account lockout after 5 failed attempts
     */
    public function testAccountLocksAfterFiveFailedAttempts()
    {
        // Create user with 4 failed attempts
        $userId = $this->userModel->insert([
            'username'           => 'testuser',
            'email'              => 'test@example.com',
            'password_hash'      => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'               => 'user',
            'status'             => 'active',
            'failed_login_count' => 4,
            'last_failed_login'  => date('Y-m-d H:i:s'),
        ]);

        $loginData = [
            'identifier' => 'test@example.com',
            'password'   => 'WrongPassword',
        ];

        $result = $this->post('auth/login', $loginData);
        
        // Should redirect back with lockout message
        $result->assertRedirect();
        $result->assertSessionHas('error');
        
        // Check account is locked
        $user = $this->userModel->find($userId);
        $this->assertNotNull($user['account_locked_until']);
        $this->assertTrue($this->userModel->isAccountLocked($userId));
    }

    /**
     * Test login with locked account
     */
    public function testLoginWithLockedAccountReturnsError()
    {
        // Create locked user
        $this->userModel->insert([
            'username'             => 'testuser',
            'email'                => 'test@example.com',
            'password_hash'        => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'                 => 'user',
            'status'               => 'active',
            'account_locked_until' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
        ]);

        $loginData = [
            'identifier' => 'test@example.com',
            'password'   => 'Password123',
        ];

        $result = $this->post('auth/login', $loginData);
        
        // Should redirect back with lockout message
        $result->assertRedirect();
        $result->assertSessionHas('error', 'Account is locked due to multiple failed login attempts. Please try again later.');
    }

    /**
     * Test login with suspended account
     */
    public function testLoginWithSuspendedAccountReturnsError()
    {
        // Create suspended user
        $this->userModel->insert([
            'username'      => 'testuser',
            'email'         => 'test@example.com',
            'password_hash' => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => 'user',
            'status'        => 'suspended',
        ]);

        $loginData = [
            'identifier' => 'test@example.com',
            'password'   => 'Password123',
        ];

        $result = $this->post('auth/login', $loginData);
        
        // Should redirect back with error
        $result->assertRedirect();
        $result->assertSessionHas('error', 'Account is not active. Please contact support.');
    }

    /**
     * Test successful login resets failed login count
     */
    public function testSuccessfulLoginResetsFailedCount()
    {
        // Create user with failed attempts
        $userId = $this->userModel->insert([
            'username'           => 'testuser',
            'email'              => 'test@example.com',
            'password_hash'      => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'               => 'user',
            'status'             => 'active',
            'failed_login_count' => 3,
            'last_failed_login'  => date('Y-m-d H:i:s'),
        ]);

        $loginData = [
            'identifier' => 'test@example.com',
            'password'   => 'Password123',
        ];

        $result = $this->post('auth/login', $loginData);
        
        // Should redirect to home
        $result->assertRedirectTo('/');
        
        // Check failed login count was reset
        $user = $this->userModel->find($userId);
        $this->assertEquals(0, $user['failed_login_count']);
        $this->assertNull($user['last_failed_login']);
        $this->assertNotNull($user['last_login']);
    }

    /**
     * Test logout destroys session
     */
    public function testLogoutDestroysSession()
    {
        // Create session
        $session = session();
        $session->set([
            'user_id'   => 1,
            'username'  => 'testuser',
            'email'     => 'test@example.com',
            'role'      => 'user',
            'logged_in' => true,
        ]);

        $result = $this->get('auth/logout');
        
        // Should redirect to home
        $result->assertRedirectTo('/');
        
        // Check session was destroyed
        $this->assertFalse($session->has('logged_in'));
        $this->assertFalse($session->has('user_id'));
    }

    /**
     * Test admin login redirects to admin dashboard
     */
    public function testAdminLoginRedirectsToDashboard()
    {
        // Create admin user
        $this->userModel->insert([
            'username'      => 'admin',
            'email'         => 'admin@example.com',
            'password_hash' => password_hash('AdminPass123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => 'admin',
            'status'        => 'active',
        ]);

        $loginData = [
            'identifier' => 'admin@example.com',
            'password'   => 'AdminPass123',
        ];

        $result = $this->post('auth/login', $loginData);
        
        // Should redirect to admin dashboard
        $result->assertRedirectTo('/admin/dashboard');
    }

    // ========================================
    // Password Reset Tests
    // ========================================

    /**
     * Test forgot password form display
     */
    public function testShowForgotPasswordDisplaysForm()
    {
        $result = $this->get('auth/forgot-password');
        
        $result->assertStatus(200);
        $result->assertSee('Forgot Password');
        $result->assertSee('Email Address');
    }

    /**
     * Test forgot password generates reset token with 60-minute expiration
     */
    public function testForgotPasswordGeneratesTokenWithSixtyMinuteExpiration()
    {
        // Create user
        $userId = $this->userModel->insert([
            'username'      => 'testuser',
            'email'         => 'test@example.com',
            'password_hash' => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'          => 'user',
            'status'        => 'active',
        ]);

        $forgotData = [
            'email' => 'test@example.com',
        ];

        $result = $this->post('auth/forgot-password', $forgotData);
        
        // Should redirect to login with success message
        $result->assertRedirectTo('/auth/login');
        $result->assertSessionHas('success');
        
        // Check reset token was generated
        $user = $this->userModel->find($userId);
        $this->assertNotNull($user['reset_token']);
        $this->assertNotNull($user['reset_token_expires']);
        
        // Verify token is 64 characters (32 bytes hex encoded)
        $this->assertEquals(64, strlen($user['reset_token']));
        
        // Verify expiration is approximately 60 minutes from now (allow 5 second variance)
        $expectedExpiration = strtotime('+60 minutes');
        $actualExpiration = strtotime($user['reset_token_expires']);
        $this->assertEqualsWithDelta($expectedExpiration, $actualExpiration, 5);
    }

    /**
     * Test forgot password with non-existent email shows success (prevents enumeration)
     */
    public function testForgotPasswordWithNonExistentEmailShowsSuccess()
    {
        $forgotData = [
            'email' => 'nonexistent@example.com',
        ];

        $result = $this->post('auth/forgot-password', $forgotData);
        
        // Should redirect to login with success message (same as valid email)
        $result->assertRedirectTo('/auth/login');
        $result->assertSessionHas('success');
    }

    /**
     * Test forgot password with invalid email format
     */
    public function testForgotPasswordWithInvalidEmailFormat()
    {
        $forgotData = [
            'email' => 'invalid-email',
        ];

        $result = $this->post('auth/forgot-password', $forgotData);
        
        // Should redirect back with error
        $result->assertRedirect();
        $result->assertSessionHas('errors');
    }

    /**
     * Test reset password form display with valid token
     */
    public function testShowResetPasswordDisplaysFormWithValidToken()
    {
        // Create user with reset token
        $resetToken = bin2hex(random_bytes(32));
        $this->userModel->insert([
            'username'            => 'testuser',
            'email'               => 'test@example.com',
            'password_hash'       => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'                => 'user',
            'status'              => 'active',
            'reset_token'         => $resetToken,
            'reset_token_expires' => date('Y-m-d H:i:s', strtotime('+60 minutes')),
        ]);

        $result = $this->get('auth/reset-password?token=' . $resetToken);
        
        $result->assertStatus(200);
        $result->assertSee('Reset Password');
        $result->assertSee('New Password');
    }

    /**
     * Test reset password form with invalid token
     */
    public function testShowResetPasswordWithInvalidToken()
    {
        $result = $this->get('auth/reset-password?token=invalid-token');
        
        // Should redirect to login with error
        $result->assertRedirectTo('/auth/login');
        $result->assertSessionHas('error');
    }

    /**
     * Test reset password form with expired token
     */
    public function testShowResetPasswordWithExpiredToken()
    {
        // Create user with expired reset token
        $resetToken = bin2hex(random_bytes(32));
        $this->userModel->insert([
            'username'            => 'testuser',
            'email'               => 'test@example.com',
            'password_hash'       => password_hash('Password123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'                => 'user',
            'status'              => 'active',
            'reset_token'         => $resetToken,
            'reset_token_expires' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
        ]);

        $result = $this->get('auth/reset-password?token=' . $resetToken);
        
        // Should redirect to login with error
        $result->assertRedirectTo('/auth/login');
        $result->assertSessionHas('error');
    }

    /**
     * Test reset password form with missing token
     */
    public function testShowResetPasswordWithMissingToken()
    {
        $result = $this->get('auth/reset-password');
        
        // Should redirect to login with error
        $result->assertRedirectTo('/auth/login');
        $result->assertSessionHas('error');
    }

    /**
     * Test successful password reset
     */
    public function testResetPasswordUpdatesPasswordAndClearsToken()
    {
        // Create user with reset token
        $resetToken = bin2hex(random_bytes(32));
        $userId = $this->userModel->insert([
            'username'            => 'testuser',
            'email'               => 'test@example.com',
            'password_hash'       => password_hash('OldPassword123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'                => 'user',
            'status'              => 'active',
            'reset_token'         => $resetToken,
            'reset_token_expires' => date('Y-m-d H:i:s', strtotime('+60 minutes')),
            'failed_login_count'  => 3,
            'last_failed_login'   => date('Y-m-d H:i:s'),
        ]);

        $resetData = [
            'token'            => $resetToken,
            'password'         => 'NewPassword123',
            'password_confirm' => 'NewPassword123',
        ];

        $result = $this->post('auth/reset-password', $resetData);
        
        // Should redirect to login with success message
        $result->assertRedirectTo('/auth/login');
        $result->assertSessionHas('success');
        
        // Check password was updated
        $user = $this->userModel->find($userId);
        $this->assertTrue(password_verify('NewPassword123', $user['password_hash']));
        
        // Check reset token was cleared
        $this->assertNull($user['reset_token']);
        $this->assertNull($user['reset_token_expires']);
        
        // Check failed login count was reset
        $this->assertEquals(0, $user['failed_login_count']);
        $this->assertNull($user['last_failed_login']);
        
        // Check account lockout was cleared
        $this->assertNull($user['account_locked_until']);
    }

    /**
     * Test reset password with password mismatch
     */
    public function testResetPasswordWithPasswordMismatch()
    {
        // Create user with reset token
        $resetToken = bin2hex(random_bytes(32));
        $this->userModel->insert([
            'username'            => 'testuser',
            'email'               => 'test@example.com',
            'password_hash'       => password_hash('OldPassword123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'                => 'user',
            'status'              => 'active',
            'reset_token'         => $resetToken,
            'reset_token_expires' => date('Y-m-d H:i:s', strtotime('+60 minutes')),
        ]);

        $resetData = [
            'token'            => $resetToken,
            'password'         => 'NewPassword123',
            'password_confirm' => 'DifferentPassword',
        ];

        $result = $this->post('auth/reset-password', $resetData);
        
        // Should redirect back with error
        $result->assertRedirect();
        $result->assertSessionHas('errors');
    }

    /**
     * Test reset password with invalid token
     */
    public function testResetPasswordWithInvalidToken()
    {
        $resetData = [
            'token'            => 'invalid-token',
            'password'         => 'NewPassword123',
            'password_confirm' => 'NewPassword123',
        ];

        $result = $this->post('auth/reset-password', $resetData);
        
        // Should redirect to login with error
        $result->assertRedirectTo('/auth/login');
        $result->assertSessionHas('error');
    }

    /**
     * Test reset password with expired token
     */
    public function testResetPasswordWithExpiredToken()
    {
        // Create user with expired reset token
        $resetToken = bin2hex(random_bytes(32));
        $this->userModel->insert([
            'username'            => 'testuser',
            'email'               => 'test@example.com',
            'password_hash'       => password_hash('OldPassword123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'                => 'user',
            'status'              => 'active',
            'reset_token'         => $resetToken,
            'reset_token_expires' => date('Y-m-d H:i:s', strtotime('-10 minutes')),
        ]);

        $resetData = [
            'token'            => $resetToken,
            'password'         => 'NewPassword123',
            'password_confirm' => 'NewPassword123',
        ];

        $result = $this->post('auth/reset-password', $resetData);
        
        // Should redirect to login with error
        $result->assertRedirectTo('/auth/login');
        $result->assertSessionHas('error');
    }

    /**
     * Test reset password with short password
     */
    public function testResetPasswordWithShortPassword()
    {
        // Create user with reset token
        $resetToken = bin2hex(random_bytes(32));
        $this->userModel->insert([
            'username'            => 'testuser',
            'email'               => 'test@example.com',
            'password_hash'       => password_hash('OldPassword123', PASSWORD_BCRYPT, ['cost' => 12]),
            'role'                => 'user',
            'status'              => 'active',
            'reset_token'         => $resetToken,
            'reset_token_expires' => date('Y-m-d H:i:s', strtotime('+60 minutes')),
        ]);

        $resetData = [
            'token'            => $resetToken,
            'password'         => 'short',
            'password_confirm' => 'short',
        ];

        $result = $this->post('auth/reset-password', $resetData);
        
        // Should redirect back with error
        $result->assertRedirect();
        $result->assertSessionHas('errors');
    }
}
