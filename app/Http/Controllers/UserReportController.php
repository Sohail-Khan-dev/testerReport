<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\Http\Request;

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
        dump('user Record is Stored SuccessFully' , $report);
    }
    public function showReporting(){
        $users = User::all();
        $projects = Project::all();
        $reports = UserReport::all();
        return view('reporting',['users'=>$users, 'projects' => $projects]);
    }

}
