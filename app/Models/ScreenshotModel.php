<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ScreenshotModel
 * 
 * Manages app screenshots for gallery display.
 * 
 * Relationships:
 * - belongsTo: app (AppModel)
 */
class ScreenshotModel extends Model
{
    protected $table            = 'screenshots';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'app_id',
        'filename',
        'file_path',
        'display_order',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'app_id'    => 'required|integer|is_not_unique[apps.id]',
        'filename'  => 'required|max_length[255]',
        'file_path' => 'required|max_length[500]',
        'display_order' => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'filename' => [
            'required'   => 'Filename is required',
            'max_length' => 'Filename cannot exceed 255 characters',
        ],
        'file_path' => [
            'required'   => 'File path is required',
            'max_length' => 'File path cannot exceed 500 characters',
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
     * Get screenshots by app
     */
    public function getByApp(int $appId): array
    {
        return $this->where('app_id', $appId)
                    ->orderBy('display_order', 'ASC')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get screenshot count for app
     */
    public function getCountByApp(int $appId): int
    {
        return $this->where('app_id', $appId)->countAllResults();
    }

    /**
     * Delete all screenshots for app
     */
    public function deleteByApp(int $appId): bool
    {
        return $this->where('app_id', $appId)->delete();
    }
}
