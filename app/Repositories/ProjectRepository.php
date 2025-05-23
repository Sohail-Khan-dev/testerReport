<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    /**
     * Specify Model class name
     * 
     * @return string
     */
    public function model(): string
    {
        return Project::class;
    }
    
    /**
     * Get projects with users
     * 
     * @return mixed
     */
    public function getProjectsWithUsers()
    {
        $cacheKey = $this->getCacheKey('getProjectsWithUsers');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->with('users')->get();
        });
    }
    
    /**
     * Get projects for a specific user
     * 
     * @param int $userId
     * @return mixed
     */
    public function getProjectsByUser(int $userId)
    {
        $cacheKey = $this->getCacheKey('getProjectsByUser', compact('userId'));
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($userId) {
            return $this->model->whereHas('users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })->get();
        });
    }
    
    /**
     * Attach users to a project
     * 
     * @param int $projectId
     * @param array $userIds
     * @return mixed
     */
    public function attachUsers(int $projectId, array $userIds)
    {
        $project = $this->find($projectId);
        $result = $project->users()->attach($userIds);
        $this->clearCache();
        
        return $result;
    }
    
    /**
     * Detach users from a project
     * 
     * @param int $projectId
     * @param array $userIds
     * @return mixed
     */
    public function detachUsers(int $projectId, array $userIds)
    {
        $project = $this->find($projectId);
        $result = $project->users()->detach($userIds);
        $this->clearCache();
        
        return $result;
    }
    
    /**
     * Sync users for a project
     * 
     * @param int $projectId
     * @param array $userIds
     * @return mixed
     */
    public function syncUsers(int $projectId, array $userIds)
    {
        $project = $this->find($projectId);
        $result = $project->users()->sync($userIds);
        $this->clearCache();
        
        return $result;
    }
}
