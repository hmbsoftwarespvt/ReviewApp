<?php

namespace App\Database\Factories;

use App\Models\AppModel;

/**
 * AppFactory
 * 
 * Generates test data for App model with realistic app information.
 */
class AppFactory extends BaseFactory
{
    /**
     * Generate app data
     */
    public function make(array $overrides = []): array
    {
        $name = $this->faker->words(3, true);
        $slug = strtolower(str_replace(' ', '-', $name)) . '-' . $this->faker->unique()->numberBetween(1, 9999);
        
        $data = [
            'name'                   => ucwords($name),
            'slug'                   => $slug,
            'description'            => $this->faker->paragraphs(3, true),
            'version'                => $this->faker->numerify('#.#.#'),
            'size'                   => $this->faker->randomElement(['5MB', '12MB', '25MB', '50MB', '100MB', '250MB']),
            'platform_type'          => $this->faker->randomElement(['android', 'ios', 'web', 'desktop']),
            'price'                  => $this->faker->randomElement([0.00, 0.99, 1.99, 2.99, 4.99, 9.99, 19.99]),
            'developer_name'         => $this->faker->company(),
            'release_date'           => $this->faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'download_url'           => $this->faker->url(),
            'trust_score'            => $this->faker->randomFloat(2, 0, 100),
            'security_score'         => $this->faker->randomFloat(2, 0, 25),
            'developer_reputation'   => $this->faker->randomFloat(2, 0, 20),
            'view_count'             => $this->faker->numberBetween(0, 10000),
            'trending_score'         => $this->faker->randomFloat(2, 0, 50),
            'approval_status'        => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'permissions'            => json_encode($this->generatePermissions()),
            'has_encryption'         => $this->faker->boolean(60), // 60% have encryption
            'third_party_sdk_count'  => $this->faker->numberBetween(0, 15),
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate approved app
     */
    public function approved(array $overrides = []): array
    {
        return $this->make(array_merge(['approval_status' => 'approved'], $overrides));
    }

    /**
     * Generate pending app
     */
    public function pending(array $overrides = []): array
    {
        return $this->make(array_merge(['approval_status' => 'pending'], $overrides));
    }

    /**
     * Generate high trust score app
     */
    public function highTrust(array $overrides = []): array
    {
        return $this->make(array_merge([
            'trust_score' => $this->faker->randomFloat(2, 80, 100),
            'security_score' => $this->faker->randomFloat(2, 20, 25),
            'developer_reputation' => $this->faker->randomFloat(2, 15, 20),
            'approval_status' => 'approved',
        ], $overrides));
    }

    /**
     * Generate low trust score app
     */
    public function lowTrust(array $overrides = []): array
    {
        return $this->make(array_merge([
            'trust_score' => $this->faker->randomFloat(2, 0, 49),
            'security_score' => $this->faker->randomFloat(2, 0, 10),
            'developer_reputation' => $this->faker->randomFloat(2, 0, 5),
            'approval_status' => 'approved',
        ], $overrides));
    }

    /**
     * Generate trending app
     */
    public function trending(array $overrides = []): array
    {
        return $this->make(array_merge([
            'view_count' => $this->faker->numberBetween(5000, 50000),
            'trending_score' => $this->faker->randomFloat(2, 30, 50),
            'approval_status' => 'approved',
        ], $overrides));
    }

    /**
     * Generate free app
     */
    public function free(array $overrides = []): array
    {
        return $this->make(array_merge(['price' => 0.00], $overrides));
    }

    /**
     * Generate paid app
     */
    public function paid(array $overrides = []): array
    {
        return $this->make(array_merge([
            'price' => $this->faker->randomElement([0.99, 1.99, 2.99, 4.99, 9.99, 19.99]),
        ], $overrides));
    }

    /**
     * Generate Android app
     */
    public function android(array $overrides = []): array
    {
        return $this->make(array_merge(['platform_type' => 'android'], $overrides));
    }

    /**
     * Generate iOS app
     */
    public function ios(array $overrides = []): array
    {
        return $this->make(array_merge(['platform_type' => 'ios'], $overrides));
    }

    /**
     * Generate web app
     */
    public function web(array $overrides = []): array
    {
        return $this->make(array_merge(['platform_type' => 'web'], $overrides));
    }

    /**
     * Generate realistic permissions array
     */
    protected function generatePermissions(): array
    {
        $allPermissions = [
            'INTERNET',
            'ACCESS_NETWORK_STATE',
            'CAMERA',
            'READ_CONTACTS',
            'WRITE_CONTACTS',
            'ACCESS_FINE_LOCATION',
            'ACCESS_COARSE_LOCATION',
            'RECORD_AUDIO',
            'READ_EXTERNAL_STORAGE',
            'WRITE_EXTERNAL_STORAGE',
            'READ_PHONE_STATE',
            'SEND_SMS',
            'RECEIVE_SMS',
            'CALL_PHONE',
            'READ_CALENDAR',
            'WRITE_CALENDAR',
        ];

        $count = $this->faker->numberBetween(2, 10);
        return $this->faker->randomElements($allPermissions, $count);
    }

    protected function getModel()
    {
        return new AppModel();
    }
}
