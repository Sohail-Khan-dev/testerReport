<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Yajra\DataTables\Exceptions\Exception;

class ProjectController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('qareport.project');
    }
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validate([
           'name' => ['required', 'string'],
           'description' => ['nullable','string']
        ]);
//        dd('here it is ', $validatedData);

        $project = Project::create($validatedData);
        return response()->json(['success'=>true , 'record'=>$project]);
    }

    /**
     * @throws Exception
     */
    public function get(): \Illuminate\Http\JsonResponse|\Yajra\DataTables\DataTableAbstract
    {
        $project = Project::all();
        return datatables($project)->make(true);
    }
}
