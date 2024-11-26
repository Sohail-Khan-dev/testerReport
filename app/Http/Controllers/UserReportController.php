<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class UserReportController extends Controller
{

    public function store(Request $request)
    {
        // dd($request->all());
        if (auth()->user()->role == 'user' || auth()->user()->role == null || $request['user_id'] == null) {
            $request['user_id'] = auth()->user()->id;
        }
        try{
            $validatedData = $request->validate([
                'user_id' => 'required',
                'project_id' => ['required', 'integer'],
                'date' => 'required|date'
            ]);
            $id = $request->id;
            // dump($id);
            $report = UserReport::updateOrCreate(['id' => $id],[
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
                'automation' => $request->input('automation'),
            ]);
        return response()->json(['success' => true,'record' => $report]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Catch any other exceptions and return a JSON response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function index(){
        $users = User::all();
        $projects = auth()->user()->projects;
        $dateOptions = $this->getDateOptions();
        return view('qareport.reporting',compact(['users','projects','dateOptions']));
    }
    public function destroy($id){
        if(UserReport::destroy($id))
            return response()->json(['success'=>true,'message' =>'Report deleted successfully.']);
        else
            return response()->json(['success'=>false, 'message'=>'Fail to delete report']);
    }
    public function edit(Request $request){
        $report = UserReport::findOrFail($request->id);
        return response()->json(['report', $report]);
    }
    public function getData()
    {
        $reports = UserReport::with(['user', 'project'])->select(['id','date','user_id','project_id','task_tested','bug_reported','regression','smoke_testing','client_meeting',
                                    'daily_meeting','mobile_testing','other','description','automation']);
        if (Gate::denies('is-admin')) {   // if Not admin then below code witll run
            // User is not an admin
            $reports->where('user_id',auth()->user()->id)    // This will get only the logedIn user Records
                ->whereDate('date', today())->get();      // This will get only today Records .

        }
        $reports->orderBy("created_at",'desc');
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
            ->addColumn('action',function($report){
                $actions = '<i class="deleteReport fa-trash fa-solid f-18 cursor-pointer" data-id="'.$report->id .'"> </i>';
                $actions .= '<i class="editReport ml-2 fa-regular fa-pen-to-square f-2x f-18 cursor-pointer" data-id="'.$report->id .'"> </i>';

                return $actions;
            })
            ->make(true);
    }
    public function getDateOptions()
    {
        return [
            'today' => Carbon::today()->toDateString(), // e.g., '2024-11-24'
            'yesterday' => Carbon::yesterday()->toDateString(), // e.g., '2024-11-23'
            'last3Days' => Carbon::today()->subDays(2)->toDateString(), // 2 days ago (including today makes it 3 days)
            'last7Days' => Carbon::today()->subDays(6)->toDateString(), // 6 days ago (including today makes it 7 days)
            'last15Days' => Carbon::today()->subDays(14)->toDateString(), // 14 days ago
            'last30Days' => Carbon::today()->subDays(29)->toDateString(), // 29 days ago
        ];
    }
    
    
}
