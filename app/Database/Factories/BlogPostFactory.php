<?php

namespace App\Database\Factories;

use App\Models\BlogPostModel;

/**
 * BlogPostFactory
 * 
 * Generates test data for BlogPost model.
 */
class BlogPostFactory extends BaseFactory
{
    /**
     * Generate blog post data
     */
    public function make(array $overrides = []): array
    {
        $title = $this->faker->sentence(6);
        $slug = strtolower(str_replace(' ', '-', trim($title, '.'))) . '-' . $this->faker->unique()->numberBetween(1, 9999);
        
        $data = [
            'title'              => $title,
            'slug'               => $slug,
            'content'            => $this->generateContent(),
            'excerpt'            => $this->faker->paragraph(3),
            'featured_image'     => $this->faker->boolean(70) ? $this->faker->imageUrl(800, 600, 'business') : null,
            'author_id'          => null, // Must be provided
            'category'           => $this->faker->randomElement(['guides', 'tips_tricks', 'scam_alerts', 'news_updates', 'reviews']),
            'publication_status' => $this->faker->randomElement(['draft', 'published']),
            'published_at'       => $this->faker->boolean(60) ? $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s') : null,
            'view_count'         => $this->faker->numberBetween(0, 5000),
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate published blog post
     */
    public function published(array $overrides = []): array
    {
        return $this->make(array_merge([
            'publication_status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s'),
        ], $overrides));
    }

    /**
     * Generate draft blog post
     */
    public function draft(array $overrides = []): array
    {
        return $this->make(array_merge([
            'publication_status' => 'draft',
            'published_at' => null,
        ], $overrides));
    }

    /**
     * Generate blog post by category
     */
    public function guides(array $overrides = []): array
    {
        return $this->make(array_merge(['category' => 'guides'], $overrides));
    }

    public function tipsTricks(array $overrides = []): array
    {
        return $this->make(array_merge(['category' => 'tips_tricks'], $overrides));
    }

    public function scamAlerts(array $overrides = []): array
    {
        return $this->make(array_merge(['category' => 'scam_alerts'], $overrides));
    }

    public function newsUpdates(array $overrides = []): array
    {
        return $this->make(array_merge(['category' => 'news_updates'], $overrides));
    }

    public function reviews(array $overrides = []): array
    {
        return $this->make(array_merge(['category' => 'reviews'], $overrides));
    }

    /**
     * Generate popular blog post
     */
    public function popular(array $overrides = []): array
    {
        return $this->make(array_merge([
            'view_count' => $this->faker->numberBetween(5000, 50000),
            'publication_status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-6 months', '-1 month')->format('Y-m-d H:i:s'),
        ], $overrides));
    }

    /**
     * Generate blog post content
     */
    protected function generateContent(): string
    {
        $paragraphs = $this->faker->numberBetween(5, 15);
        $content = '';

        for ($i = 0; $i < $paragraphs; $i++) {
            $content .= '<p>' . $this->faker->paragraph($this->faker->numberBetween(3, 8)) . '</p>' . "\n\n";
            
            // Occasionally add a heading
            if ($i > 0 && $i % 3 === 0) {
                $content .= '<h2>' . $this->faker->sentence(4) . '</h2>' . "\n\n";
            }
            
            // Occasionally add a list
            if ($i > 0 && $i % 5 === 0) {
                $content .= '<ul>' . "\n";
                for ($j = 0; $j < $this->faker->numberBetween(3, 6); $j++) {
                    $content .= '<li>' . $this->faker->sentence() . '</li>' . "\n";
                }
                $content .= '</ul>' . "\n\n";
            }
        }

        return $content;
    }

    protected function getModel()
    {
        return new BlogPostModel();
    }
}
