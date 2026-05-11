<?php

namespace App\Database\Factories;

use App\Models\ActivityLogModel;

/**
 * ActivityLogFactory
 * 
 * Generates test data for ActivityLog model.
 */
class ActivityLogFactory extends BaseFactory
{
    /**
     * Generate activity log data
     */
    public function make(array $overrides = []): array
    {
        $data = [
            'app_id'        => null, // Must be provided
            'activity_type' => $this->faker->randomElement(['view', 'review', 'scam_report']),
            'activity_date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'count'         => $this->faker->numberBetween(1, 100),
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate view activity
     */
    public function view(array $overrides = []): array
    {
        return $this->make(array_merge(['activity_type' => 'view'], $overrides));
    }

    /**
     * Generate review activity
     */
    public function review(array $overrides = []): array
    {
        return $this->make(array_merge(['activity_type' => 'review'], $overrides));
    }

    /**
     * Generate scam report activity
     */
    public function scamReport(array $overrides = []): array
    {
        return $this->make(array_merge(['activity_type' => 'scam_report'], $overrides));
    }

    /**
     * Generate today's activity
     */
    public function today(array $overrides = []): array
    {
        return $this->make(array_merge(['activity_date' => date('Y-m-d')], $overrides));
    }

    /**
     * Generate yesterday's activity
     */
    public function yesterday(array $overrides = []): array
    {
        return $this->make(array_merge([
            'activity_date' => date('Y-m-d', strtotime('-1 day')),
        ], $overrides));
    }

    /**
     * Generate high activity count
     */
    public function highActivity(array $overrides = []): array
    {
        return $this->make(array_merge([
            'count' => $this->faker->numberBetween(500, 5000),
        ], $overrides));
    }

    /**
     * Generate 24-hour metrics for an app
     */
    public function create24HourMetrics(int $appId): array
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $model = $this->getModel();
        $ids = [];

        // Create view activity
        $viewData = $this->make([
            'app_id' => $appId,
            'activity_type' => 'view',
            'activity_date' => $yesterday,
            'count' => $this->faker->numberBetween(50, 500),
        ]);
        $id = $model->insert($viewData);
        if ($id !== false) {
            $ids[] = $id;
        }

        // Create review activity
        $reviewData = $this->make([
            'app_id' => $appId,
            'activity_type' => 'review',
            'activity_date' => $yesterday,
            'count' => $this->faker->numberBetween(5, 50),
        ]);
        $id = $model->insert($reviewData);
        if ($id !== false) {
            $ids[] = $id;
        }

        // Create scam report activity (optional)
        if ($this->faker->boolean(30)) {
            $scamData = $this->make([
                'app_id' => $appId,
                'activity_type' => 'scam_report',
                'activity_date' => $yesterday,
                'count' => $this->faker->numberBetween(1, 10),
            ]);
            $id = $model->insert($scamData);
            if ($id !== false) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Generate trending app metrics (high activity)
     */
    public function createTrendingMetrics(int $appId): array
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $model = $this->getModel();
        $ids = [];

        // High view count
        $viewData = $this->make([
            'app_id' => $appId,
            'activity_type' => 'view',
            'activity_date' => $yesterday,
            'count' => $this->faker->numberBetween(150, 1000),
        ]);
        $id = $model->insert($viewData);
        if ($id !== false) {
            $ids[] = $id;
        }

        // High review count
        $reviewData = $this->make([
            'app_id' => $appId,
            'activity_type' => 'review',
            'activity_date' => $yesterday,
            'count' => $this->faker->numberBetween(15, 100),
        ]);
        $id = $model->insert($reviewData);
        if ($id !== false) {
            $ids[] = $id;
        }

        return $ids;
    }

    protected function getModel()
    {
        return new ActivityLogModel();
    }
}
