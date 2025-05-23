<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    
    /**
     * @var int
     */
    protected $cacheTime = 60; // Cache time in minutes
    
    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->makeModel();
    }
    
    /**
     * Specify Model class name
     * 
     * @return string
     */
    abstract public function model(): string;
    
    /**
     * Make model instance
     * 
     * @return Model
     * @throws \Exception
     */
    public function makeModel()
    {
        $model = app($this->model());
        
        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
        
        return $this->model = $model;
    }
    
    /**
     * Get all records
     * 
     * @param array $columns
     * @return mixed
     */
    public function all(array $columns = ['*'])
    {
        $cacheKey = $this->getCacheKey('all', $columns);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($columns) {
            return $this->model->all($columns);
        });
    }
    
    /**
     * Get paginated records
     * 
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate(int $perPage = 15, array $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }
    
    /**
     * Create a new record
     * 
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $model = $this->model->create($data);
        $this->clearCache();
        
        return $model;
    }
    
    /**
     * Update a record
     * 
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function update(array $data, int $id)
    {
        $model = $this->model->findOrFail($id);
        $model->update($data);
        $this->clearCache();
        
        return $model;
    }
    
    /**
     * Delete a record
     * 
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        $result = $this->model->destroy($id);
        $this->clearCache();
        
        return $result;
    }
    
    /**
     * Find a record by ID
     * 
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function find(int $id, array $columns = ['*'])
    {
        $cacheKey = $this->getCacheKey('find', compact('id', 'columns'));
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($id, $columns) {
            return $this->model->findOrFail($id, $columns);
        });
    }
    
    /**
     * Find a record by field
     * 
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return mixed
     */
    public function findByField(string $field, $value, array $columns = ['*'])
    {
        $cacheKey = $this->getCacheKey('findByField', compact('field', 'value', 'columns'));
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($field, $value, $columns) {
            return $this->model->where($field, $value)->get($columns);
        });
    }
    
    /**
     * Find a record by multiple fields
     * 
     * @param array $where
     * @param array $columns
     * @return mixed
     */
    public function findWhere(array $where, array $columns = ['*'])
    {
        $cacheKey = $this->getCacheKey('findWhere', compact('where', 'columns'));
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($where, $columns) {
            $query = $this->model;
            
            foreach ($where as $field => $value) {
                if (is_array($value)) {
                    list($field, $condition, $val) = $value;
                    $query = $query->where($field, $condition, $val);
                } else {
                    $query = $query->where($field, '=', $value);
                }
            }
            
            return $query->get($columns);
        });
    }
    
    /**
     * Find or create a record
     * 
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function firstOrCreate(array $attributes, array $values = [])
    {
        $model = $this->model->firstOrCreate($attributes, $values);
        $this->clearCache();
        
        return $model;
    }
    
    /**
     * Update or create a record
     * 
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $model = $this->model->updateOrCreate($attributes, $values);
        $this->clearCache();
        
        return $model;
    }
    
    /**
     * Generate cache key
     * 
     * @param string $method
     * @param array $args
     * @return string
     */
    protected function getCacheKey(string $method, array $args = [])
    {
        $key = strtolower(class_basename($this->model)) . ':' . $method;
        
        if (count($args)) {
            $key .= ':' . md5(serialize($args));
        }
        
        return $key;
    }
    
    /**
     * Clear cache
     * 
     * @return void
     */
    protected function clearCache()
    {
        $cacheKey = strtolower(class_basename($this->model)) . ':*';
        
        // Get all cache keys that match the pattern
        $keys = Cache::getStore()->many([$cacheKey]);
        
        // Delete all matching keys
        foreach ($keys as $key => $value) {
            Cache::forget($key);
        }
    }
}
