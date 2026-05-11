<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * SettingModel
 * 
 * Manages platform configuration settings.
 */
class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'setting_key'  => 'required|max_length[100]|is_unique[settings.setting_key,id,{id}]',
        'setting_type' => 'permit_empty|in_list[string,integer,float,boolean,json]',
    ];

    protected $validationMessages = [
        'setting_key' => [
            'required'   => 'Setting key is required',
            'max_length' => 'Setting key cannot exceed 100 characters',
            'is_unique'  => 'Setting key must be unique',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['updateTimestamp'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Update timestamp on update
     */
    protected function updateTimestamp(array $data): array
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Get setting by key
     */
    public function get(string $key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return $this->castValue($setting['setting_value'], $setting['setting_type']);
    }

    /**
     * Set setting value
     */
    public function setSetting(string $key, $value, string $type = 'string', ?string $description = null): bool
    {
        $existing = $this->where('setting_key', $key)->first();
        
        $data = [
            'setting_key'   => $key,
            'setting_value' => $this->prepareValue($value, $type),
            'setting_type'  => $type,
            'description'   => $description,
        ];
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        }
        
        return $this->insert($data) !== false;
    }

    /**
     * Get all settings as key-value array
     */
    public function getAll(): array
    {
        $settings = $this->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->castValue(
                $setting['setting_value'],
                $setting['setting_type']
            );
        }
        
        return $result;
    }

    /**
     * Get settings by prefix
     */
    public function getByPrefix(string $prefix): array
    {
        $settings = $this->like('setting_key', $prefix, 'after')->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->castValue(
                $setting['setting_value'],
                $setting['setting_type']
            );
        }
        
        return $result;
    }

    /**
     * Delete setting by key
     */
    public function deleteByKey(string $key): bool
    {
        return $this->where('setting_key', $key)->delete();
    }

    /**
     * Cast value based on type
     */
    protected function castValue($value, string $type)
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            case 'string':
            default:
                return (string) $value;
        }
    }

    /**
     * Prepare value for storage
     */
    protected function prepareValue($value, string $type): string
    {
        switch ($type) {
            case 'json':
                return json_encode($value);
            case 'boolean':
                return $value ? '1' : '0';
            default:
                return (string) $value;
        }
    }

    /**
     * Get trust algorithm weights
     */
    public function getTrustAlgorithmWeights(): array
    {
        return [
            'review_rating_weight'      => $this->get('trust_algorithm.review_rating_weight', 30),
            'security_score_weight'     => $this->get('trust_algorithm.security_score_weight', 25),
            'developer_reputation_weight' => $this->get('trust_algorithm.developer_reputation_weight', 20),
            'scam_report_weight'        => $this->get('trust_algorithm.scam_report_weight', 15),
            'app_age_weight'            => $this->get('trust_algorithm.app_age_weight', 10),
        ];
    }

    /**
     * Set trust algorithm weights
     */
    public function setTrustAlgorithmWeights(array $weights): bool
    {
        $success = true;
        
        foreach ($weights as $key => $value) {
            $fullKey = 'trust_algorithm.' . $key;
            $success = $success && $this->set($fullKey, $value, 'integer');
        }
        
        return $success;
    }
}
