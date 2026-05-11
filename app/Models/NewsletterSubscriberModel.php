<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * NewsletterSubscriberModel
 * 
 * Manages newsletter subscriptions with confirmation and unsubscribe tokens.
 */
class NewsletterSubscriberModel extends Model
{
    protected $table            = 'newsletter_subscribers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'email',
        'unsubscribe_token',
        'is_confirmed',
        'confirmation_token',
        'email_count_today',
        'last_email_date',
        'subscribed_at',
        'unsubscribed_at',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'email' => 'required|valid_email|max_length[255]|is_unique[newsletter_subscribers.email,id,{id}]',
        'unsubscribe_token' => 'required|max_length[100]',
    ];

    protected $validationMessages = [
        'email' => [
            'required'     => 'Email is required',
            'valid_email'  => 'Email must be a valid email address',
            'is_unique'    => 'Email is already subscribed',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateTokens'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generate unsubscribe and confirmation tokens
     */
    protected function generateTokens(array $data): array
    {
        if (!isset($data['data']['unsubscribe_token'])) {
            $data['data']['unsubscribe_token'] = bin2hex(random_bytes(32));
        }
        
        if (!isset($data['data']['confirmation_token'])) {
            $data['data']['confirmation_token'] = bin2hex(random_bytes(32));
        }
        
        if (!isset($data['data']['subscribed_at'])) {
            $data['data']['subscribed_at'] = date('Y-m-d H:i:s');
        }
        
        return $data;
    }

    /**
     * Find subscriber by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Find subscriber by unsubscribe token
     */
    public function findByUnsubscribeToken(string $token): ?array
    {
        return $this->where('unsubscribe_token', $token)->first();
    }

    /**
     * Find subscriber by confirmation token
     */
    public function findByConfirmationToken(string $token): ?array
    {
        return $this->where('confirmation_token', $token)->first();
    }

    /**
     * Confirm subscription
     */
    public function confirmSubscription(int $subscriberId): bool
    {
        return $this->update($subscriberId, [
            'is_confirmed'       => true,
            'confirmation_token' => null,
        ]);
    }

    /**
     * Unsubscribe
     */
    public function unsubscribe(int $subscriberId): bool
    {
        return $this->update($subscriberId, [
            'unsubscribed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get all confirmed subscribers
     */
    public function getConfirmed(): array
    {
        return $this->where('is_confirmed', true)
                    ->where('unsubscribed_at', null)
                    ->findAll();
    }

    /**
     * Check if subscriber can receive email (daily limit check)
     */
    public function canReceiveEmail(int $subscriberId): bool
    {
        $subscriber = $this->find($subscriberId);
        
        if (!$subscriber) {
            return false;
        }
        
        // Check if last email was today
        $today = date('Y-m-d');
        
        if ($subscriber['last_email_date'] !== $today) {
            // Reset counter for new day
            $this->update($subscriberId, [
                'email_count_today' => 0,
                'last_email_date'   => $today,
            ]);
            return true;
        }
        
        // Check daily limit (max 5 emails per day)
        return $subscriber['email_count_today'] < 5;
    }

    /**
     * Increment email count
     */
    public function incrementEmailCount(int $subscriberId): bool
    {
        $subscriber = $this->find($subscriberId);
        
        if (!$subscriber) {
            return false;
        }
        
        $today = date('Y-m-d');
        
        // Reset counter if it's a new day
        if ($subscriber['last_email_date'] !== $today) {
            return $this->update($subscriberId, [
                'email_count_today' => 1,
                'last_email_date'   => $today,
            ]);
        }
        
        // Increment counter
        return $this->set('email_count_today', 'email_count_today + 1', false)
                    ->where('id', $subscriberId)
                    ->update();
    }

    /**
     * Get subscriber count
     */
    public function getSubscriberCount(): int
    {
        return $this->where('is_confirmed', true)
                    ->where('unsubscribed_at', null)
                    ->countAllResults();
    }
}
