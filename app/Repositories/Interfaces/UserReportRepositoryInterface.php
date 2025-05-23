<?php

namespace App\Repositories\Interfaces;

interface UserReportRepositoryInterface extends RepositoryInterface
{
    /**
     * Get reports for a specific user
     * 
     * @param int $userId
     * @param string|null $date
     * @return mixed
     */
    public function getReportsByUser(int $userId, ?string $date = null);
    
    /**
     * Get reports for a specific project
     * 
     * @param int $projectId
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return mixed
     */
    public function getReportsByProject(int $projectId, ?string $fromDate = null, ?string $toDate = null);
    
    /**
     * Get reports for a date range
     * 
     * @param string $fromDate
     * @param string $toDate
     * @param int|null $userId
     * @param int|null $projectId
     * @return mixed
     */
    public function getReportsByDateRange(string $fromDate, string $toDate, ?int $userId = null, ?int $projectId = null);
    
    /**
     * Get reports with user and project data
     * 
     * @param array $filters
     * @return mixed
     */
    public function getReportsWithRelations(array $filters = []);
}
