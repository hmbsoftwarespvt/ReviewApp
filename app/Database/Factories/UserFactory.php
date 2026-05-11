<?php

namespace App\Database\Factories;

use App\Models\UserModel;

/**
 * UserFactory
 * 
 * Generates test data for User model with hashed passwords.
 */
class UserFactory extends BaseFactory
{
    /**
     * Generate user data
     */
    public function make(array $overrides = []): array
    {
        $data = [
            'username'              => $this->faker->unique()->userName(),
            'email'                 => $this->faker->unique()->safeEmail(),
            'password_hash'         => password_hash('password123', PASSWORD_DEFAULT),
            'role'                  => $this->faker->randomElement(['user', 'admin']),
            'status'                => $this->faker->randomElement(['active', 'suspended', 'deleted']),
            'email_verified'        => $this->faker->boolean(80), // 80% verified
            'verification_token'    => $this->faker->boolean(20) ? bin2hex(random_bytes(32)) : null,
            'reset_token'           => null,
            'reset_token_expires'   => null,
            'failed_login_count'    => $this->faker->numberBetween(0, 3),
            'last_failed_login'     => $this->faker->boolean(30) ? $this->faker->dateTimeBetween('-7 days', 'now')->format('Y-m-d H:i:s') : null,
            'account_locked_until'  => null,
            'last_login'            => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s') : null,
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate admin user
     */
    public function admin(array $overrides = []): array
    {
        return $this->make(array_merge(['role' => 'admin', 'status' => 'active'], $overrides));
    }

    /**
     * Generate regular user
     */
    public function user(array $overrides = []): array
    {
        return $this->make(array_merge(['role' => 'user', 'status' => 'active'], $overrides));
    }

    /**
     * Generate verified user
     */
    public function verified(array $overrides = []): array
    {
        return $this->make(array_merge([
            'email_verified' => true,
            'verification_token' => null,
            'status' => 'active',
        ], $overrides));
    }

    /**
     * Generate suspended user
     */
    public function suspended(array $overrides = []): array
    {
        return $this->make(array_merge(['status' => 'suspended'], $overrides));
    }

    /**
     * Generate user with password reset token
     */
    public function withResetToken(array $overrides = []): array
    {
        return $this->make(array_merge([
            'reset_token' => bin2hex(random_bytes(32)),
            'reset_token_expires' => date('Y-m-d H:i:s', strtotime('+60 minutes')),
        ], $overrides));
    }

    protected function getModel()
    {
        return new UserModel();
    }
}
