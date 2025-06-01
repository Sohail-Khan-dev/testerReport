<?php

namespace App\Services;

use App\Http\Resources\UserReportResource;
use App\Repositories\Interfaces\UserReportRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class UserReportService
{
    /**
     * @var UserReportRepositoryInterface
     */
    protected $userReportRepository;

    /**
     * UserReportService constructor.
     *
     * @param UserReportRepositoryInterface $userReportRepository
     */
    public function __construct(UserReportRepositoryInterface $userReportRepository)
    {
        $this->userReportRepository = $userReportRepository;
    }

    /**
     * Store a new user report
     *
     * @param array $data
     * @return mixed
     */
    public function storeReport(array $data)
    {
        // If user is not admin or manager, set user_id to current user
        if (Auth::user()->role === 'user' || Auth::user()->role === null || !isset($data['user_id'])) {
            $data['user_id'] = Auth::id();
        }

        // Handle checkbox fields
        $checkboxFields = [
            'regression',
            'smoke_testing',
            'client_meeting',
            'daily_meeting',
            'mobile_testing',
            'automation',
        ];

        foreach ($checkboxFields as $field) {
            if (!isset($data[$field])) {
                $data[$field] = 0;
            }
        }

        // Check if we're updating or creating
        if (isset($data['id']) && !empty($data['id'])) {
            return $this->userReportRepository->updateOrCreate(['id' => $data['id']], $data);
        }

        return $this->userReportRepository->create($data);
    }

    /**
     * Get a user report by ID
     *
     * @param int $id
     * @return mixed
     */
    public function getReport(int $id)
    {
        $report = $this->userReportRepository->find($id);
        return new UserReportResource($report);
    }

    /**
     * Delete a user report
     *
     * @param int $id
     * @return bool
     */
    public function deleteReport(int $id)
    {
        return $this->userReportRepository->delete($id);
    }

    /**
     * Get reports for DataTables
     *
     * @param array $filters
     * @return mixed
     */
    public function getReportsForDataTable(array $filters = [])
    {
        $query = $this->userReportRepository->getReportsWithRelations($filters);

        // Apply access restrictions
        if (Gate::denies('is-admin')) {
            // User is not an admin, show only their reports for today
            $query->where('user_id', Auth::id())
                  ->whereDate('date', Carbon::today());
        } else {
            // Admin can see all reports with filters
            if (isset($filters['from_date']) && isset($filters['to_date'])) {
                $query->whereBetween('date', [$filters['from_date'], $filters['to_date']]);
            }

            if (isset($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }

            if (isset($filters['project_id'])) {
                $query->where('project_id', $filters['project_id']);
            }
        }

        // Order by created_at desc by default
        $query->orderBy('created_at', 'desc');
        // I want to show the time with date when it is displayed. 
        
        return DataTables::of($query)
            ->filterColumn('user_name', function($query, $keyword) {
                $query->whereHas('user', function($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('project_name', function($query, $keyword) {
                $query->whereHas('project', function($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('user_name', function($report) {
                return $report->user->name;
            })
            ->addColumn('project_name', function($report) {
                return $report->project->name;
            })
            ->editColumn('date', function($report) {
                // Format date with time
                // dd($report);
                $date = Carbon::parse($report->date);
                $time = Carbon::parse($report->created_at)->format('H:i:s');
                // Add 5 hour to the time 
                $time = Carbon::parse($time)->addHours(5)->format('H:i:s');
                return $date->format('Y-m-d') . ' ' . $time;
            })
            ->addColumn('action', function($report) {
                $actions = '<i class="deleteReport fa-trash fa-solid f-18 cursor-pointer" data-id="'.$report->id .'"> </i>';
                $actions .= '<i class="editReport ml-2 fa-regular fa-pen-to-square f-2x f-18 cursor-pointer" data-id="'.$report->id .'"> </i>';

                return $actions;
            })
            ->make(true);
    }

    /**
     * Get date options for filtering
     *
     * @return array
     */
    public function getDateOptions()
    {
        return [
            'today' => Carbon::today()->toDateString(),
            'yesterday' => Carbon::yesterday()->toDateString(),
            'last3Days' => Carbon::today()->subDays(2)->toDateString(),
            'last7Days' => Carbon::today()->subDays(6)->toDateString(),
            'last15Days' => Carbon::today()->subDays(14)->toDateString(),
            'last30Days' => Carbon::today()->subDays(29)->toDateString(),
        ];
    }
}
