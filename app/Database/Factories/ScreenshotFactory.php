<?php

namespace App\Database\Factories;

use App\Models\ScreenshotModel;

/**
 * ScreenshotFactory
 * 
 * Generates test data for Screenshot model.
 */
class ScreenshotFactory extends BaseFactory
{
    /**
     * Generate screenshot data
     */
    public function make(array $overrides = []): array
    {
        $filename = 'screenshot-' . $this->faker->unique()->numberBetween(1000, 9999) . '.png';
        
        $data = [
            'app_id'        => null, // Must be provided
            'filename'      => $filename,
            'file_path'     => '/uploads/screenshots/' . $filename,
            'display_order' => $this->faker->numberBetween(0, 10),
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate multiple screenshots for an app
     */
    public function forApp(int $appId, int $count = 5): array
    {
        $screenshots = [];
        
        for ($i = 0; $i < $count; $i++) {
            $screenshots[] = $this->make([
                'app_id' => $appId,
                'display_order' => $i,
            ]);
        }
        
        return $screenshots;
    }

    /**
     * Create multiple screenshots for an app
     */
    public function createForApp(int $appId, int $count = 5): array
    {
        $ids = [];
        $model = $this->getModel();
        
        for ($i = 0; $i < $count; $i++) {
            $data = $this->make([
                'app_id' => $appId,
                'display_order' => $i,
            ]);
            
            $id = $model->insert($data);
            if ($id !== false) {
                $ids[] = $id;
            }
        }
        
        return $ids;
    }

    protected function getModel()
    {
        return new ScreenshotModel();
    }
}
