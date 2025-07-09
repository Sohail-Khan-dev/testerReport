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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $users = User::select('id', 'name')->get();
        $projects = auth()->user()->projects;
        $allprojects = Project::select('id', 'name')->get();
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
        // Data will be fetched via AJAX, so we pass an empty collection initially.
        $userData = collect();
        $dateOptions = $this->userReportService->getDateOptions();
        $users = User::pluck('name', 'id');
        $projects = Project::pluck('name', 'id');
        // dd($users, $projects);
        return view('qareport.dashboard', compact('userData', 'dateOptions', 'users', 'projects'));
    }
    
    public function getDashboardDataAjax(Request $request)
    {
        try {
            $query = UserReport::query()
                ->join('projects', 'user_reports.project_id', '=', 'projects.id')
                ->join('users', 'user_reports.user_id', '=', 'users.id')
                ->select(
                    'projects.name as project_name',
                    DB::raw('CAST(SUM(user_reports.task_tested) AS UNSIGNED) as tasks_tested'),
                    DB::raw('CAST(SUM(user_reports.bug_reported) AS UNSIGNED) as bugs_reported'),
                    DB::raw('CAST(SUM(CASE WHEN user_reports.daily_meeting = 1 THEN 1 ELSE 0 END) AS UNSIGNED) as daily_meeting'),
                    DB::raw('CAST(SUM(CASE WHEN user_reports.client_meeting = 1 THEN 1 ELSE 0 END) AS UNSIGNED) as client_meeting'),
                    DB::raw('CAST(SUM(CASE WHEN user_reports.regression = 1 THEN 1 ELSE 0 END) AS UNSIGNED) as regression'),
                    DB::raw('CAST(SUM(CASE WHEN user_reports.smoke_testing = 1 THEN 1 ELSE 0 END) AS UNSIGNED) as smoke_testing'),
                    DB::raw('CAST(SUM(CASE WHEN user_reports.mobile_testing = 1 THEN 1 ELSE 0 END) AS UNSIGNED) as mobile_testing'),
                    DB::raw('CAST(SUM(CASE WHEN user_reports.automation = 1 THEN 1 ELSE 0 END) AS UNSIGNED) as automation')
                )
                ->groupBy('projects.name');

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereBetween('user_reports.date', [$request->from_date, $request->to_date]);
            }

            if ($request->filled('user') && $request->user !== 'All Users') {
                $query->where('user_reports.user_id', $request->user);
            }

            if ($request->filled('project') && $request->project !== 'All Projects') {
                $query->where('user_reports.project_id', $request->project);
            }

            $aggregatedData = $query->get();

            // Log for debugging
            Log::info('Dashboard query executed successfully', [
                'filters' => $request->only(['from_date', 'to_date', 'user', 'project']),
                'result_count' => $aggregatedData->count(),
                'sql' => $query->toSql()
            ]);

            return response()->json(['userData' => $aggregatedData]);

        } catch (\Exception $e) {
            Log::error('Dashboard data fetch error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to fetch dashboard data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
