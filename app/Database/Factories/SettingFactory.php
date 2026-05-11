<?php

namespace App\Database\Factories;

use App\Models\SettingModel;

/**
 * SettingFactory
 * 
 * Generates test data for Setting model.
 */
class SettingFactory extends BaseFactory
{
    /**
     * Generate setting data
     */
    public function make(array $overrides = []): array
    {
        $type = $this->faker->randomElement(['string', 'integer', 'float', 'boolean', 'json']);
        
        $data = [
            'setting_key'   => $this->faker->unique()->word() . '.' . $this->faker->word(),
            'setting_value' => $this->generateValueForType($type),
            'setting_type'  => $type,
            'description'   => $this->faker->sentence(10),
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate trust algorithm weight settings
     */
    public function trustAlgorithmWeights(array $overrides = []): array
    {
        $weights = [
            'trust_algorithm.review_rating_weight' => 30,
            'trust_algorithm.security_score_weight' => 25,
            'trust_algorithm.developer_reputation_weight' => 20,
            'trust_algorithm.scam_report_weight' => 15,
            'trust_algorithm.app_age_weight' => 10,
        ];

        $settings = [];
        foreach ($weights as $key => $value) {
            $settings[] = $this->make(array_merge([
                'setting_key' => $key,
                'setting_value' => (string) $value,
                'setting_type' => 'integer',
                'description' => "Weight for {$key} in trust score calculation",
            ], $overrides));
        }

        return $settings;
    }

    /**
     * Create trust algorithm weight settings
     */
    public function createTrustAlgorithmWeights(): array
    {
        $settings = $this->trustAlgorithmWeights();
        $model = $this->getModel();
        $ids = [];

        foreach ($settings as $setting) {
            $id = $model->insert($setting);
            if ($id !== false) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Generate email notification settings
     */
    public function emailSettings(array $overrides = []): array
    {
        return [
            $this->make(array_merge([
                'setting_key' => 'email.sender_name',
                'setting_value' => 'AppTrust Platform',
                'setting_type' => 'string',
                'description' => 'Email sender name',
            ], $overrides)),
            $this->make(array_merge([
                'setting_key' => 'email.sender_address',
                'setting_value' => 'noreply@apptrust.com',
                'setting_type' => 'string',
                'description' => 'Email sender address',
            ], $overrides)),
            $this->make(array_merge([
                'setting_key' => 'email.daily_limit',
                'setting_value' => '5',
                'setting_type' => 'integer',
                'description' => 'Maximum emails per subscriber per day',
            ], $overrides)),
        ];
    }

    /**
     * Generate pagination settings
     */
    public function paginationSettings(array $overrides = []): array
    {
        return [
            $this->make(array_merge([
                'setting_key' => 'pagination.search_results',
                'setting_value' => '20',
                'setting_type' => 'integer',
                'description' => 'Items per page for search results',
            ], $overrides)),
            $this->make(array_merge([
                'setting_key' => 'pagination.category_pages',
                'setting_value' => '24',
                'setting_type' => 'integer',
                'description' => 'Items per page for category pages',
            ], $overrides)),
            $this->make(array_merge([
                'setting_key' => 'pagination.blog_listings',
                'setting_value' => '12',
                'setting_type' => 'integer',
                'description' => 'Items per page for blog listings',
            ], $overrides)),
        ];
    }

    /**
     * Generate value based on type
     */
    protected function generateValueForType(string $type): string
    {
        switch ($type) {
            case 'integer':
                return (string) $this->faker->numberBetween(1, 100);
            case 'float':
                return (string) $this->faker->randomFloat(2, 0, 100);
            case 'boolean':
                return $this->faker->boolean() ? '1' : '0';
            case 'json':
                return json_encode([
                    'key1' => $this->faker->word(),
                    'key2' => $this->faker->numberBetween(1, 100),
                ]);
            case 'string':
            default:
                return $this->faker->sentence();
        }
    }

    protected function getModel()
    {
        return new SettingModel();
    }
}
