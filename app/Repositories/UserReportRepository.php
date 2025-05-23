<?php

namespace App\Repositories;

use App\Models\UserReport;
use App\Repositories\Interfaces\UserReportRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UserReportRepository extends BaseRepository implements UserReportRepositoryInterface
{
    /**
     * Specify Model class name
     * 
     * @return string
     */
    public function model(): string
    {
        return UserReport::class;
    }
    
    /**
     * Get reports for a specific user
     * 
     * @param int $userId
     * @param string|null $date
     * @return mixed
     */
    public function getReportsByUser(int $userId, ?string $date = null)
    {
        $cacheKey = $this->getCacheKey('getReportsByUser', compact('userId', 'date'));
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($userId, $date) {
            $query = $this->model->where('user_id', $userId);
            
            if ($date) {
                $query->whereDate('date', $date);
            }
            
            return $query->with(['user', 'project'])->get();
        });
    }
    
    /**
     * Get reports for a specific project
     * 
     * @param int $projectId
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return mixed
     */
    public function getReportsByProject(int $projectId, ?string $fromDate = null, ?string $toDate = null)
    {
        $cacheKey = $this->getCacheKey('getReportsByProject', compact('projectId', 'fromDate', 'toDate'));
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($projectId, $fromDate, $toDate) {
            $query = $this->model->where('project_id', $projectId);
            
            if ($fromDate && $toDate) {
                $query->whereBetween('date', [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->whereDate('date', '>=', $fromDate);
            } elseif ($toDate) {
                $query->whereDate('date', '<=', $toDate);
            }
            
            return $query->with(['user', 'project'])->get();
        });
    }
    
    /**
     * Get reports for a date range
     * 
     * @param string $fromDate
     * @param string $toDate
     * @param int|null $userId
     * @param int|null $projectId
     * @return mixed
     */
    public function getReportsByDateRange(string $fromDate, string $toDate, ?int $userId = null, ?int $projectId = null)
    {
        $cacheKey = $this->getCacheKey('getReportsByDateRange', compact('fromDate', 'toDate', 'userId', 'projectId'));
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($fromDate, $toDate, $userId, $projectId) {
            $query = $this->model->whereBetween('date', [$fromDate, $toDate]);
            
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            if ($projectId) {
                $query->where('project_id', $projectId);
            }
            
            return $query->with(['user', 'project'])->get();
        });
    }
    
    /**
     * Get reports with user and project data
     * 
     * @param array $filters
     * @return mixed
     */
    public function getReportsWithRelations(array $filters = [])
    {
        $query = $this->model->with(['user', 'project']);
        
        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (isset($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        
        if (isset($filters['from_date']) && isset($filters['to_date'])) {
            $query->whereBetween('date', [$filters['from_date'], $filters['to_date']]);
        } elseif (isset($filters['from_date'])) {
            $query->whereDate('date', '>=', $filters['from_date']);
        } elseif (isset($filters['to_date'])) {
            $query->whereDate('date', '<=', $filters['to_date']);
        } elseif (isset($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }
        
        // Apply sorting
        if (isset($filters['sort_by']) && isset($filters['sort_direction'])) {
            $query->orderBy($filters['sort_by'], $filters['sort_direction']);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        return $query;
    }
}
