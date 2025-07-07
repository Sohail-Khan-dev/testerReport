<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserReportRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\UserReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\UserReport;

class UserReportController extends Controller
{
    /**
     * @var UserReportService
     */
    protected $userReportService;

    /**
     * UserReportController constructor.
     *
     * @param UserReportService $userReportService
     */
    public function __construct(UserReportService $userReportService)
    {
        $this->userReportService = $userReportService;
    }

    /**
     * Store a newly created report in storage.
     *
     * @param StoreUserReportRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserReportRequest $request): JsonResponse
    {
        try {
            $report = $this->userReportService->storeReport($request->validated());

            return response()->json([
                'success' => true,
                'record' => $report,
                'message' => 'Report saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the reporting view.
     *
     * @return View
     */
    public function index(): View
    {
        $users = User::all();
        $projects = auth()->user()->projects;
        $allprojects = Project::all();
        $dateOptions = $this->userReportService->getDateOptions();

        return view('qareport.reporting', compact([
            'users',
            'projects',
            'dateOptions',
            'allprojects'
        ]));
    }

    /**
     * Remove the specified report from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->userReportService->deleteReport($id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Report deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete report'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a report for editing.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        try {
            $report = $this->userReportService->getReport($request->id);

            return response()->json(['report', $report]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data for DataTables.
     *
     * @param Request $request
     * @return mixed
     */
    public function getData(Request $request)
    {
        $filters = $request->only([
            'from_date',
            'to_date',
            'user_id',
            'project_id'
        ]);

        return $this->userReportService->getReportsForDataTable($filters);
    }
public function viewDashboard(Request $request): View
    {
        $userData = $this->getDashboardData($request);
        $dateOptions = $this->userReportService->getDateOptions();
        $users = User::pluck('id', 'name');
        $projects = Project::pluck('id', 'name');

        return view('qareport.dashboard', compact('userData', 'dateOptions', 'users', 'projects'));
    }
    public function getDashboardData(Request $request)
    {
        $query = UserReport::with(['user', 'project']);
        // Apply date filters if provided
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }else{
            // Default to last 30 days if no date filters are provided
            $query->where('date', '>=', now()->subDays(30));
        }

        // Apply user filter if provided
        if ($request->has('user')) {
            $query->where('user_id', $request->user);
        }

        // Apply project filter if provided
        if ($request->has('project')) {
            $query->where('project_id', $request->project);
        }
        
        // Get all reports with relationships
        $reports = $query->get();
        
        // Group and aggregate data by user name and project name
        $userData = $reports->groupBy(function($report) {
            return $report->project->name;
        })->map(function($projectReports) {
                return [
                    'tasks_tested' => $projectReports->sum('task_tested'),
                    'bugs_reported' => $projectReports->sum('bug_reported'),
                    // 'regression_testing' => $projectReports->sum('regression'),
                    // 'smoke_testing' => $projectReports->sum('smoke_testing'),
                    // 'client_meeting' => $projectReports->sum('client_meeting'),
                    'daily_meeting' => $projectReports->sum('daily_meeting'),
                    // 'mobile_testing' => $projectReports->sum('mobile_testing'),
                    // 'automation_testing' => $projectReports->sum('automation'),
                    // 'other' => $projectReports->sum('other')
                ];
            });
        return $userData;
        return response()->json([
            'userData' => $userData
        ]);
        // $users = User::pluck('id', 'name');
        // $projects = Project::pluck('id', 'name');

        // return view('qareport.dashboard', compact('userData', 'dateOptions', 'users', 'projects'));
    }
}


