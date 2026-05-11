<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UserModel
 * 
 * Manages user accounts with authentication and authorization.
 * 
 * Relationships:
 * - hasMany: reviews (ReviewModel)
 * - hasMany: scam_reports (ScamReportModel)
 * - hasMany: blog_posts (BlogPostModel) as author
 */
class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'email',
        'password_hash',
        'role',
        'status',
        'email_verified',
        'verification_token',
        'reset_token',
        'reset_token_expires',
        'failed_login_count',
        'last_failed_login',
        'account_locked_until',
        'last_login',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'password_hash' => 'permit_empty|min_length[60]|max_length[255]',
        'role'     => 'permit_empty|in_list[user,admin]',
        'status'   => 'permit_empty|in_list[active,suspended,deleted]',
    ];

    protected $validationMessages = [
        'username' => [
            'required'      => 'Username is required',
            'min_length'    => 'Username must be at least 3 characters',
            'max_length'    => 'Username cannot exceed 50 characters',
            'is_unique'     => 'Username is already taken',
        ],
        'email' => [
            'required'      => 'Email is required',
            'valid_email'   => 'Email must be a valid email address',
            'is_unique'     => 'Email is already registered',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get user by email or username
     */
    public function findByEmailOrUsername(string $identifier): ?array
    {
        return $this->where('email', $identifier)
                    ->orWhere('username', $identifier)
                    ->first();
    }

    /**
     * Get user reviews
     */
    public function getReviews(int $userId): array
    {
        $reviewModel = new \App\Models\ReviewModel();
        return $reviewModel->where('user_id', $userId)->findAll();
    }

    /**
     * Get user scam reports
     */
    public function getScamReports(int $userId): array
    {
        $scamReportModel = new \App\Models\ScamReportModel();
        return $scamReportModel->where('user_id', $userId)->findAll();
    }

    /**
     * Check if account is locked
     */
    public function isAccountLocked(int $userId): bool
    {
        $user = $this->find($userId);
        
        if (!$user || !$user['account_locked_until']) {
            return false;
        }

        $lockedUntil = strtotime($user['account_locked_until']);
        return $lockedUntil > time();
    }

    /**
     * Increment failed login count
     */
    public function incrementFailedLogin(int $userId): bool
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return false;
        }

        return $this->update($userId, [
            'failed_login_count' => $user['failed_login_count'] + 1,
            'last_failed_login'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Reset failed login count
     */
    public function resetFailedLogin(int $userId): bool
    {
        return $this->update($userId, [
            'failed_login_count' => 0,
            'last_failed_login'  => null,
        ]);
    }

    /**
     * Lock account for specified minutes
     */
    public function lockAccount(int $userId, int $minutes = 30): bool
    {
        $lockUntil = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));
        
        return $this->update($userId, [
            'account_locked_until' => $lockUntil,
        ]);
    }
}
