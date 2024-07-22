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
        // dd($request);
        $validatedData = $request->validate([
            'user_id' => 'required|not_in:None',
            'project_id' => 'required',
            'date' => 'required|date'
        ]);
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
        // $reports = $this->showReports();
        return redirect()->route('reporting');

    }
    public function index(){
        $users = User::all();
        $projects = Project::all();
        return view('reporting',compact(['users','projects']));
    }

    public function getData()
    {
        $reports = UserReport::with(['user', 'project']);

        return DataTables::of($reports)
            ->addColumn('date',function($report){
                return $report->date;
            })
            ->addColumn('user_name', function($report) {
                return $report->user->name;
            })
            ->addColumn('project_name', function($report) {
                return $report->project->name;
            })
            ->addColumn('tasks', function($report) {
                return $report->task_tested;
            })
            ->addColumn('bugs', function($report) {
                return $report->bug_reported;
            })
            ->addColumn('regressions', function($report) {
                return $report->regression;
            })
            ->addColumn('smokes', function($report) {
                return $report->smoke_testing;
            })
            ->addColumn('client_meetings', function($report) {
                return $report->client_meeting;
            })
            ->addColumn('daily_meeting', function($report) {
                return $report->daily_meeting;
            })
            ->addColumn('mobiles', function($report) {
                return $report->mobile_testing;
            })
            ->addColumn('other', function($report) {
                return $report->other;
            })
            ->addColumn('description', function($report) {
                // dump($report->description);
                return $report->description ?? 'No description';
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
