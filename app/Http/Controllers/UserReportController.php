<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;

class UserReportController extends Controller
{
    //

    public function store(Request $request)
    {
//        dump('Call is her for the request');
        if (auth()->user()->role == 'user' || auth()->user()->role == null || $request['user_id'] == null) {
            $request['user_id'] = auth()->user()->id;
        }
//        dd($request['user_id']);

        $validatedData = $request->validate([
            'user_id' => 'required',
            'project_id' => ['required', 'integer'],
            'date' => 'required|date'
        ]);
//        dd("after auth validation ");

        $report = UserReport::create([
            'user_id' => $request->input('user_id'),
            'project_id' => $request->input('project_id'),
            'date' => $request->input('date'),
            'task_tested' => $request->input('task_tested'),
            'bug_reported' => $request->input('bug_reported'),
            'other' => $request->input('other'),
            'regression' => $request->input('regression'),
            'smoke_testing' => $request->input('smoke_testing'),
            'client_meeting' => $request->input('client_meeting'),
            'daily_meeting' => $request->input('daily_meeting'),
            'mobile_testing' => $request->input('mobile_testing'),
            'description' => $request->input('description'),
        ]);
//dd($report);
        return response()->json(['success' => true,'record' => $report]);
    }
    public function index(){
        $users = User::all();
        $projects = Project::all();
        return view('qareport.reporting',compact(['users','projects']));
    }

    public function getData()
    {
        $reports = UserReport::with(['user', 'project'])->select(['date','user_id','project_id','task_tested','bug_reported','regression','smoke_testing','client_meeting',
                                    'daily_meeting','mobile_testing','other','description']);
        if (Gate::denies('is-admin')) {   // if Not admin then below code witll run
            // User is not an admin
            $reports->where('user_id',auth()->user()->id)    // This will get only the logedIn user Records
                ->whereDate('date', today())->get();      // This will get only today Records .
//            dd('This is the User not Admin ');
        }

        return DataTables::of($reports)
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
            ->make(true);
    }

    // public function showReports(){
    //     $reports = UserReport::with(['user','project'])->get();
    //     $total_tasks = $reports->sum('task_tested');
    //     $total_bugs = $reports->sum('bug_reported');
    //     $total_regression = $reports->sum('regression');
    //     $total_smoke = $reports->sum('smoke_testing');
    //     $total_client_meeting = $reports->sum('client_meeting');
    //     $total_daily_meeting = $reports->sum('daily_meeting');
    //     $total_mobile = $reports->sum('mobile_testing');
    //     $users = User::all();
    //     $projects = Project::all();
    //     return view('reporting', compact('reports','users','projects','total_tasks','total_bugs','total_regression','total_smoke',
    //                                      'total_client_meeting','total_daily_meeting','total_mobile'));
    // }

    // public function getUserReports(){
    //     $reports = UserReport::with(['user','project'])->get();
    //     $users = User::all();
    //     $projects = Project::all();
    //     return view('reporting',['users'=>$users, 'projects' => $projects,'reports'=>$reports]);
    // }
}
