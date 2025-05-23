<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * @var ProjectService
     */
    protected $projectService;

    /**
     * ProjectController constructor.
     *
     * @param ProjectService $projectService
     */
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display the project view.
     *
     * @return View
     */
    public function index(): View
    {
        return view('qareport.project');
    }

    /**
     * Store a newly created project in storage.
     *
     * @param ProjectRequest $request
     * @return JsonResponse
     */
    public function store(ProjectRequest $request): JsonResponse
    {
        try {
            $project = $this->projectService->storeProject($request->validated());

            return response()->json([
                'success' => true,
                'record' => $project,
                'message' => 'Project created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all projects for DataTables.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->projectService->getProjectsForDataTable();
    }

    /**
     * Update the specified project in storage.
     *
     * @param ProjectRequest $request
     * @return JsonResponse
     */
    public function update(ProjectRequest $request): JsonResponse
    {
        try {
            $project = $this->projectService->updateProject($request->validated());

            return response()->json([
                'success' => true,
                'record' => $project,
                'message' => 'Project updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified project from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->projectService->deleteProject($id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete project'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting project: ' . $e->getMessage()
            ], 500);
        }
    }
}
