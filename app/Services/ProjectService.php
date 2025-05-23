<?php

namespace App\Services;

use App\Http\Resources\ProjectResource;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Yajra\DataTables\Facades\DataTables;

class ProjectService
{
    /**
     * @var ProjectRepositoryInterface
     */
    protected $projectRepository;

    /**
     * ProjectService constructor.
     *
     * @param ProjectRepositoryInterface $projectRepository
     */
    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Store a new project
     *
     * @param array $data
     * @return mixed
     */
    public function storeProject(array $data)
    {
        $project = $this->projectRepository->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // If user_ids are provided, sync them with the project
        if (isset($data['user_ids']) && is_array($data['user_ids'])) {
            $this->projectRepository->syncUsers($project->id, $data['user_ids']);
        }

        return $project;
    }

    /**
     * Update a project
     *
     * @param array $data
     * @return mixed
     */
    public function updateProject(array $data)
    {
        $project = $this->projectRepository->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ], $data['id']);

        // If user_ids are provided, sync them with the project
        if (isset($data['user_ids']) && is_array($data['user_ids'])) {
            $this->projectRepository->syncUsers($project->id, $data['user_ids']);
        }

        return $project;
    }

    /**
     * Delete a project
     *
     * @param int $id
     * @return bool
     */
    public function deleteProject(int $id)
    {
        return $this->projectRepository->delete($id);
    }

    /**
     * Get all projects
     *
     * @return mixed
     */
    public function getAllProjects()
    {
        $projects = $this->projectRepository->all();
        return ProjectResource::collection($projects);
    }

    /**
     * Get projects for a specific user
     *
     * @param int $userId
     * @return mixed
     */
    public function getProjectsByUser(int $userId)
    {
        $projects = $this->projectRepository->getProjectsByUser($userId);
        return ProjectResource::collection($projects);
    }

    /**
     * Get projects for DataTables
     *
     * @return mixed
     */
    public function getProjectsForDataTable()
    {
        $projects = $this->projectRepository->all();

        return DataTables::of($projects)
            ->addColumn('action', function($project) {
                $actions = '<div class="d-flex">';
                $actions .= '<button class="btn btn-sm btn-primary edit-project me-2" data-id="'.$project->id.'">Edit</button>';
                $actions .= '<button class="btn btn-sm btn-danger delete-project" data-id="'.$project->id.'">Delete</button>';
                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
