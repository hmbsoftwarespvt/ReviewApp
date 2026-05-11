<?php

namespace App\Database\Factories;

use Faker\Factory as FakerFactory;
use Faker\Generator;

/**
 * BaseFactory
 * 
 * Base class for all model factories providing Faker instance and common utilities.
 */
abstract class BaseFactory
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    /**
     * Generate a single record
     */
    abstract public function make(array $overrides = []): array;

    /**
     * Generate multiple records
     */
    public function makeMany(int $count, array $overrides = []): array
    {
        $records = [];
        
        for ($i = 0; $i < $count; $i++) {
            $records[] = $this->make($overrides);
        }
        
        return $records;
    }

    /**
     * Create and insert a single record
     */
    public function create(array $overrides = []): int
    {
        $data = $this->make($overrides);
        $model = $this->getModel();
        
        $id = $model->insert($data);
        
        if ($id === false) {
            throw new \RuntimeException('Failed to create record: ' . json_encode($model->errors()));
        }
        
        return $id;
    }

    /**
     * Create and insert multiple records
     */
    public function createMany(int $count, array $overrides = []): array
    {
        $ids = [];
        
        for ($i = 0; $i < $count; $i++) {
            $ids[] = $this->create($overrides);
        }
        
        return $ids;
    }

    /**
     * Get the model instance for this factory
     */
    abstract protected function getModel();

    /**
     * Merge overrides with generated data
     */
    protected function mergeOverrides(array $data, array $overrides): array
    {
        return array_merge($data, $overrides);
    }
}
