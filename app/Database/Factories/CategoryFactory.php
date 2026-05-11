<?php

namespace App\Database\Factories;

use App\Models\CategoryModel;

/**
 * CategoryFactory
 * 
 * Generates test data for Category model.
 */
class CategoryFactory extends BaseFactory
{
    /**
     * Generate category data
     */
    public function make(array $overrides = []): array
    {
        $name = $this->faker->unique()->words(2, true);
        $slug = strtolower(str_replace(' ', '-', $name));
        
        $data = [
            'name'          => ucwords($name),
            'slug'          => $slug,
            'description'   => $this->faker->sentence(15),
            'icon'          => $this->faker->randomElement([
                'fa-mobile',
                'fa-dollar-sign',
                'fa-robot',
                'fa-video',
                'fa-wallet',
                'fa-bitcoin',
                'fa-palette',
                'fa-users',
                'fa-briefcase',
                'fa-gamepad',
                'fa-graduation-cap',
                'fa-heartbeat',
                'fa-plane',
            ]),
            'display_order' => $this->faker->numberBetween(0, 100),
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate predefined category
     */
    public function predefined(string $categoryName): array
    {
        $categories = [
            'Earning Apps' => ['slug' => 'earning-apps', 'icon' => 'fa-dollar-sign', 'display_order' => 1],
            'AI Tools' => ['slug' => 'ai-tools', 'icon' => 'fa-robot', 'display_order' => 2],
            'Video Editing' => ['slug' => 'video-editing', 'icon' => 'fa-video', 'display_order' => 3],
            'Finance' => ['slug' => 'finance', 'icon' => 'fa-wallet', 'display_order' => 4],
            'Shopping' => ['slug' => 'shopping', 'icon' => 'fa-shopping-cart', 'display_order' => 5],
            'Crypto' => ['slug' => 'crypto', 'icon' => 'fa-bitcoin', 'display_order' => 6],
            'Design Tools' => ['slug' => 'design-tools', 'icon' => 'fa-palette', 'display_order' => 7],
            'Social Media' => ['slug' => 'social-media', 'icon' => 'fa-users', 'display_order' => 8],
            'Productivity' => ['slug' => 'productivity', 'icon' => 'fa-briefcase', 'display_order' => 9],
            'Gaming' => ['slug' => 'gaming', 'icon' => 'fa-gamepad', 'display_order' => 10],
            'Education' => ['slug' => 'education', 'icon' => 'fa-graduation-cap', 'display_order' => 11],
            'Health' => ['slug' => 'health', 'icon' => 'fa-heartbeat', 'display_order' => 12],
            'Travel' => ['slug' => 'travel', 'icon' => 'fa-plane', 'display_order' => 13],
        ];

        if (!isset($categories[$categoryName])) {
            throw new \InvalidArgumentException("Unknown predefined category: {$categoryName}");
        }

        return $this->make(array_merge([
            'name' => $categoryName,
            'description' => "Apps in the {$categoryName} category",
        ], $categories[$categoryName]));
    }

    /**
     * Create all predefined categories
     */
    public function createAllPredefined(): array
    {
        $categoryNames = [
            'Earning Apps',
            'AI Tools',
            'Video Editing',
            'Finance',
            'Shopping',
            'Crypto',
            'Design Tools',
            'Social Media',
            'Productivity',
            'Gaming',
            'Education',
            'Health',
            'Travel',
        ];

        $ids = [];
        foreach ($categoryNames as $name) {
            $data = $this->predefined($name);
            $model = $this->getModel();
            $id = $model->insert($data);
            if ($id !== false) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    protected function getModel()
    {
        return new CategoryModel();
    }
}
