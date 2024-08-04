<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserReportController extends Controller
{
    //
    public function store(Request $request){

        if(auth()->user()->role == 'user'){
            $request['user_id'] = auth()->user()->id ;
        }
        $validatedData = $request->validate([
            'user_id' => 'required|not_in:None',
            'project_id' => 'required',
            'date' => 'required|date'
        ]);
        if($validatedData){
            $report =  UserReport::create([
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
            return redirect()->route('reporting');
        }
        else{
            return response()->json([
                'success' => false,
                'errors' => $validatedData->errors()
            ], 422);
        }
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
