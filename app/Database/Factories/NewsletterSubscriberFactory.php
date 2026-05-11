<?php

namespace App\Database\Factories;

use App\Models\NewsletterSubscriberModel;

/**
 * NewsletterSubscriberFactory
 * 
 * Generates test data for NewsletterSubscriber model.
 */
class NewsletterSubscriberFactory extends BaseFactory
{
    /**
     * Generate newsletter subscriber data
     */
    public function make(array $overrides = []): array
    {
        $data = [
            'email'              => $this->faker->unique()->safeEmail(),
            'unsubscribe_token'  => bin2hex(random_bytes(32)),
            'is_confirmed'       => $this->faker->boolean(70), // 70% confirmed
            'confirmation_token' => $this->faker->boolean(30) ? bin2hex(random_bytes(32)) : null,
            'email_count_today'  => $this->faker->numberBetween(0, 5),
            'last_email_date'    => $this->faker->boolean(50) ? date('Y-m-d') : $this->faker->dateTimeBetween('-7 days', '-1 day')->format('Y-m-d'),
            'subscribed_at'      => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
            'unsubscribed_at'    => null,
        ];

        return $this->mergeOverrides($data, $overrides);
    }

    /**
     * Generate confirmed subscriber
     */
    public function confirmed(array $overrides = []): array
    {
        return $this->make(array_merge([
            'is_confirmed' => true,
            'confirmation_token' => null,
        ], $overrides));
    }

    /**
     * Generate unconfirmed subscriber
     */
    public function unconfirmed(array $overrides = []): array
    {
        return $this->make(array_merge([
            'is_confirmed' => false,
            'confirmation_token' => bin2hex(random_bytes(32)),
        ], $overrides));
    }

    /**
     * Generate unsubscribed subscriber
     */
    public function unsubscribed(array $overrides = []): array
    {
        return $this->make(array_merge([
            'unsubscribed_at' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
        ], $overrides));
    }

    /**
     * Generate subscriber at email limit
     */
    public function atEmailLimit(array $overrides = []): array
    {
        return $this->make(array_merge([
            'email_count_today' => 5,
            'last_email_date' => date('Y-m-d'),
            'is_confirmed' => true,
        ], $overrides));
    }

    protected function getModel()
    {
        return new NewsletterSubscriberModel();
    }
}
