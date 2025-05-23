<?php

namespace App\Repositories\Interfaces;

interface ProjectRepositoryInterface extends RepositoryInterface
{
    /**
     * Get projects with users
     * 
     * @return mixed
     */
    public function getProjectsWithUsers();
    
    /**
     * Get projects for a specific user
     * 
     * @param int $userId
     * @return mixed
     */
    public function getProjectsByUser(int $userId);
    
    /**
     * Attach users to a project
     * 
     * @param int $projectId
     * @param array $userIds
     * @return mixed
     */
    public function attachUsers(int $projectId, array $userIds);
    
    /**
     * Detach users from a project
     * 
     * @param int $projectId
     * @param array $userIds
     * @return mixed
     */
    public function detachUsers(int $projectId, array $userIds);
    
    /**
     * Sync users for a project
     * 
     * @param int $projectId
     * @param array $userIds
     * @return mixed
     */
    public function syncUsers(int $projectId, array $userIds);
}
