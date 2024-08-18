<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Yajra\DataTables\Exceptions\Exception;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        event(new Registered($user));   // this call an event whcih make the the user $this->user i.e current login User

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    public function storeNew(Request $request){
        if($request['role'] == null){
            $request['role'] = 'admin';
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        return response()->json(['success'=>true, 'user'=>$user]);
    }
    public function index(){
        return view('qareport.user');
    }
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        if(User::destroy($id))
            return response()->json(['success'=>true,'message' => 'User deleted successfully.']);
        else
            return response()->json(['Fail to delete']);
//        $user = User::Find($id);
//        if($user)
//          $user->delete();
    }
    /**
     * @throws Exception
     */
    public function getAllUser(){
        $users = User::select(['id','name','email','role']);
        return datatables($users)
            ->addColumn('action',function ($row){
                $buttons = '<a href="' . route('login', $row->id) . '" class="btn btn-sm btn-primary rounded">Login</a>';
                $buttons .= '<a href="' . route('profile.edit', $row->id) . '" class="btn btn-sm btn-warning rounded">Edit</a>';
                if($row->id != auth()->user()->id)
                    $buttons .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-danger deleteUser rounded">Delete</a>';

                // Enable/Disable toggle (You can use a condition to display the correct state)
                if ($row->is_active) {
                    $buttons .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-secondary toggleStatus rounded">Disable</a>';
                } else {
                    $buttons .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-sm btn-success toggleStatus rounded">Enable</a>';
                }
                return "<div class='users-button-div'>". $buttons . "</div>";
            })
            ->make(true);
    }

}
