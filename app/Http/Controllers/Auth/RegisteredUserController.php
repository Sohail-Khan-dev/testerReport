<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
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

    public function storeNew(Request $request): JsonResponse
    {

        if($request['role'] == null){
            $request['role'] = 'admin';
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->id],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        if($request->id){
            //dump('Here is updated User');
            $user = User::find($request->id);

            if($user){
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->role = $request->role;
                $user->save();
            }else{
               return response()->json(['success'=>false,'message'=>'No user Found']);
            }
        }else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);
        }
        if($user)
            return response()->json(['success' => true, 'user' => $user]);

    }
    public function index(){
        return view('qareport.user');
    }
    public function destroy($id): JsonResponse
    {
        if(User::destroy($id))
            return response()->json(['success'=>true,'message' => 'User deleted successfully.']);
        else
            return response()->json(['Fail to delete']);
    }
    public function edit(Request $request){
        $user = User::findOrFail($request->id);
        return response()->json(['user'=>$user]);
    }

    public function directLogin($id): RedirectResponse
    {
        $user = User::find($id);
        if($user){
            Auth::login($user);
            return redirect()->route('home')->with('success','You have logged in as '. $user->name);
        }
        return redirect()->back()->with('error', 'User not found');
    }

    /**
     * @throws Exception
     */
    public function getAllUser(){
        $users = User::select(['id','name','email','role']);
        return datatables($users)
            ->addColumn('action',function ($row){

                $buttons = '<a href="' . route('profile.edit', $row->id) . '" class="" id="edit-user" data-id='.$row->id.'><i class="fa-regular fa-pen-to-square f-2x f-18 cursor-pointer"></i></a>';
                if($row->id != auth()->user()->id) {
                    $buttons .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="deleteUser"><i class="fa-solid fa-trash f-18 cursor-pointer"></i></a>';
                    $buttons .= '<a href="javascript:void(0)" data-id="'.$row->id.'"   class="loginUser"><i class="fa fa-sign-in f-18 cursor-pointer" aria-hidden="true"></i></a>';
                }
                // Enable/Disable toggle (You can use a condition to display the correct state)
//                if ($row->is_active) {
//                    $buttons .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="toggleStatus"><i class="fa fa-refresh f-18"></i></a>';
//                } else {
//                    $buttons .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="toggleStatus"><i class="fa fa-refresh f-18"></i></a>';
//                }
                return "<div class='users-button-div'>". $buttons . "</div>";
            })
            ->make(true);
    }

}
