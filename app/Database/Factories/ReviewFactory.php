<?php

namespace App\Database\Factories;

use App\Models\ReviewModel;

/**
 * ReviewFactory
 * 
 * Generates test data for Review model with ratings 1-5.
 */
class ReviewFactory extends BaseFactory
{
    /**
     * Generate review data
     */
    public function make(array $overrides = []): array
    {
        $rating = $this->faker->numberBetween(1, 5);
        
        $data = [
            'app_id'          => null, // Must be provided
            'user_id'         => null, // Must be provided
            'rating'          => $rating,
            'title'           => $this->generateTitle($rating),
            'review_text'     => $this->generateReviewText($rating),
            'pros'            => $this->faker->boolean(70) ? $this->faker->sentence(10) : null,
            'cons'            => $this->faker->boolean(70) ? $this->faker->sentence(10) : null,
            'approval_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'helpful_count'   => $this->faker->numberBetween(0, 50),
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate approved review
     */
    public function approved(array $overrides = []): array
    {
        return $this->make(array_merge(['approval_status' => 'approved'], $overrides));
    }

    /**
     * Generate pending review
     */
    public function pending(array $overrides = []): array
    {
        return $this->make(array_merge(['approval_status' => 'pending'], $overrides));
    }

    /**
     * Generate rejected review
     */
    public function rejected(array $overrides = []): array
    {
        return $this->make(array_merge(['approval_status' => 'rejected'], $overrides));
    }

    /**
     * Generate 5-star review
     */
    public function fiveStars(array $overrides = []): array
    {
        return $this->make(array_merge([
            'rating' => 5,
            'title' => $this->generateTitle(5),
            'review_text' => $this->generateReviewText(5),
        ], $overrides));
    }

    /**
     * Generate 1-star review
     */
    public function oneStar(array $overrides = []): array
    {
        return $this->make(array_merge([
            'rating' => 1,
            'title' => $this->generateTitle(1),
            'review_text' => $this->generateReviewText(1),
        ], $overrides));
    }

    /**
     * Generate helpful review (high helpful count)
     */
    public function helpful(array $overrides = []): array
    {
        return $this->make(array_merge([
            'helpful_count' => $this->faker->numberBetween(50, 200),
            'approval_status' => 'approved',
        ], $overrides));
    }

    /**
     * Generate review title based on rating
     */
    protected function generateTitle(int $rating): string
    {
        $positiveTitles = [
            'Excellent app!',
            'Highly recommended',
            'Best app ever',
            'Amazing experience',
            'Love this app',
            'Perfect for my needs',
            'Outstanding quality',
            'Exceeded expectations',
        ];

        $negativeTitles = [
            'Disappointing',
            'Not worth it',
            'Terrible experience',
            'Waste of time',
            'Buggy and unreliable',
            'Poor quality',
            'Avoid this app',
            'Complete disaster',
        ];

        $neutralTitles = [
            'Decent app',
            'It\'s okay',
            'Average experience',
            'Could be better',
            'Mixed feelings',
            'Has potential',
        ];

        if ($rating >= 4) {
            return $this->faker->randomElement($positiveTitles);
        } elseif ($rating <= 2) {
            return $this->faker->randomElement($negativeTitles);
        } else {
            return $this->faker->randomElement($neutralTitles);
        }
    }

    /**
     * Generate review text based on rating (50-2000 chars)
     */
    protected function generateReviewText(int $rating): string
    {
        $positiveTexts = [
            'This app has completely transformed how I work. The interface is intuitive and the features are exactly what I needed. ',
            'I\'ve been using this app for months now and it keeps getting better with each update. Highly recommended! ',
            'The developers really know what they\'re doing. Everything works smoothly and the customer support is excellent. ',
            'I was skeptical at first, but this app has proven to be incredibly reliable and useful. Worth every penny! ',
        ];

        $negativeTexts = [
            'This app is full of bugs and crashes constantly. I\'ve tried reinstalling multiple times but nothing helps. ',
            'Very disappointed with this purchase. The app doesn\'t deliver on its promises and customer support is non-existent. ',
            'I regret downloading this app. It\'s slow, confusing, and doesn\'t work as advertised. Save your money! ',
            'The worst app I\'ve ever used. It drains my battery, takes up too much space, and barely functions. ',
        ];

        $neutralTexts = [
            'This app has some good features but also several issues that need to be addressed. It works for basic tasks. ',
            'It\'s an okay app, nothing special. Does what it says but could use some improvements in the user interface. ',
            'Mixed experience with this app. Some features work great while others are buggy. Hoping for updates soon. ',
        ];

        if ($rating >= 4) {
            $base = $this->faker->randomElement($positiveTexts);
        } elseif ($rating <= 2) {
            $base = $this->faker->randomElement($negativeTexts);
        } else {
            $base = $this->faker->randomElement($neutralTexts);
        }

        // Add more sentences to meet minimum 50 characters requirement
        $text = $base . $this->faker->sentences($this->faker->numberBetween(2, 5), true);

        // Ensure it's within 50-2000 character range
        if (strlen($text) < 50) {
            $text .= ' ' . $this->faker->paragraph();
        }

        return substr($text, 0, 2000);
    }

    protected function getModel()
    {
        return new ReviewModel();
    }
}
