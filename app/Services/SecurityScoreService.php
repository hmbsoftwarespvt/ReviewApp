<?php

namespace App\Services;

use App\Models\AppModel;

/**
 * SecurityScoreService
 * 
 * Calculates security score component (0-25 points) based on:
 * - Permission count
 * - Sensitive permissions usage
 * - Encryption status
 * - Third-party SDK count
 */
class SecurityScoreService
{
    protected AppModel $appModel;
    
    protected array $sensitivePermissions = [
        'location',
        'contacts',
        'camera',
        'microphone',
    ];
    
    public function __construct()
    {
        $this->appModel = new AppModel();
    }
    
    /**
     * Calculate security score for an app
     * 
     * @param int $appId
     * @return float Security score (0-25)
     */
    public function calculateSecurityScore(int $appId): float
    {
        $app = $this->appModel->find($appId);
        
        if (!$app) {
            return 0.0;
        }
        
        $score = 0.0;
        
        // 1. Permission count score (2-8 points)
        $score += $this->analyzePermissions($app);
        
        // 2. Encryption bonus (+5 points)
        if ($this->checkEncryption($app)) {
            $score += 5;
        }
        
        // 3. Third-party SDK penalty (-2 points if > 5)
        $sdkCount = $this->countThirdPartySDKs($app);
        if ($sdkCount > 5) {
            $score -= 2;
        }
        
        // Ensure score is between 0 and 25
        $score = max(0, min(25, $score));
        
        // Update app record
        $this->appModel->update($appId, ['security_score' => $score]);
        
        return $score;
    }
    
    /**
     * Analyze permissions and calculate score
     * 
     * Base score:
     * - < 5 permissions: 8 points
     * - 5-10 permissions: 5 points
     * - > 10 permissions: 2 points
     * 
     * Penalties:
     * - Each sensitive permission: -3 points
     * 
     * @param array $app
     * @return float Permission score
     */
    public function analyzePermissions(array $app): float
    {
        $permissions = [];
        
        if (!empty($app['permissions'])) {
            $permissions = is_string($app['permissions']) 
                ? json_decode($app['permissions'], true) 
                : $app['permissions'];
        }
        
        if (!is_array($permissions)) {
            $permissions = [];
        }
        
        $permissionCount = count($permissions);
        
        // Base score based on permission count
        $score = 0;
        
        if ($permissionCount < 5) {
            $score = 8;
        } elseif ($permissionCount <= 10) {
            $score = 5;
        } else {
            $score = 2;
        }
        
        // Deduct points for sensitive permissions
        $sensitiveCount = $this->countSensitivePermissions($permissions);
        $score -= ($sensitiveCount * 3);
        
        return $score;
    }
    
    /**
     * Check if app uses encryption
     * 
     * @param array $app
     * @return bool
     */
    public function checkEncryption(array $app): bool
    {
        return !empty($app['has_encryption']) && $app['has_encryption'] == 1;
    }
    
    /**
     * Count third-party SDKs
     * 
     * @param array $app
     * @return int
     */
    public function countThirdPartySDKs(array $app): int
    {
        return (int) ($app['third_party_sdk_count'] ?? 0);
    }
    
    /**
     * Count sensitive permissions in the permissions array
     * 
     * @param array $permissions
     * @return int
     */
    protected function countSensitivePermissions(array $permissions): int
    {
        $count = 0;
        
        foreach ($permissions as $permission) {
            $permissionLower = strtolower($permission);
            
            foreach ($this->sensitivePermissions as $sensitive) {
                if (strpos($permissionLower, $sensitive) !== false) {
                    $count++;
                    break; // Count each permission only once
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Get detailed security analysis
     * 
     * @param int $appId
     * @return array
     */
    public function getSecurityAnalysis(int $appId): array
    {
        $app = $this->appModel->find($appId);
        
        if (!$app) {
            return [
                'score' => 0,
                'permission_count' => 0,
                'sensitive_permissions' => 0,
                'has_encryption' => false,
                'third_party_sdk_count' => 0,
                'breakdown' => [],
            ];
        }
        
        $permissions = [];
        if (!empty($app['permissions'])) {
            $permissions = is_string($app['permissions']) 
                ? json_decode($app['permissions'], true) 
                : $app['permissions'];
        }
        
        if (!is_array($permissions)) {
            $permissions = [];
        }
        
        $permissionCount = count($permissions);
        $sensitiveCount = $this->countSensitivePermissions($permissions);
        $hasEncryption = $this->checkEncryption($app);
        $sdkCount = $this->countThirdPartySDKs($app);
        
        // Calculate breakdown
        $breakdown = [];
        
        // Permission count contribution
        if ($permissionCount < 5) {
            $breakdown['permission_count'] = ['points' => 8, 'label' => 'Low permission count (< 5)'];
        } elseif ($permissionCount <= 10) {
            $breakdown['permission_count'] = ['points' => 5, 'label' => 'Moderate permission count (5-10)'];
        } else {
            $breakdown['permission_count'] = ['points' => 2, 'label' => 'High permission count (> 10)'];
        }
        
        // Sensitive permissions penalty
        if ($sensitiveCount > 0) {
            $breakdown['sensitive_permissions'] = [
                'points' => -($sensitiveCount * 3),
                'label' => "Sensitive permissions ({$sensitiveCount} found)",
            ];
        }
        
        // Encryption bonus
        if ($hasEncryption) {
            $breakdown['encryption'] = ['points' => 5, 'label' => 'Uses encryption'];
        }
        
        // SDK penalty
        if ($sdkCount > 5) {
            $breakdown['third_party_sdks'] = [
                'points' => -2,
                'label' => "Many third-party SDKs ({$sdkCount} found)",
            ];
        }
        
        return [
            'score' => $this->calculateSecurityScore($appId),
            'permission_count' => $permissionCount,
            'sensitive_permissions' => $sensitiveCount,
            'has_encryption' => $hasEncryption,
            'third_party_sdk_count' => $sdkCount,
            'breakdown' => $breakdown,
        ];
    }
}
