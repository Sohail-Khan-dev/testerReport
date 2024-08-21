<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

Route::get('/', function () {
    if(Auth::check()){
//        dd('here we are : ' , auth()->user()->role);

        if(Gate::allows('is-admin'))
            return redirect()->route('dashboard');
        if(Gate::allows('is-user')) {
            return redirect()->route('reporting');
        }
    }
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    if(Gate::allows('is-admin'))
        return view('qareport.dashboard');
    else if(Gate::allows('is-user'))
        return redirect()->route('reporting');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::middleware(['is-admin'])->group(function (){
        Route::post('register/new', [RegisteredUserController::class, 'storeNew'])->name('register.new');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/user/{id}', [RegisteredUserController::class, 'destroy'])->name('user.destroy');
        Route::get('/login-direct/{id}', [RegisteredUserController::class, 'directLogin'])->name('user.destroy');
        // Below is for the User  Routes We have to Update the profile routes and Use this for User
        Route::get('/users',[RegisteredUserController::class,'index'])->name('users');
        Route::get('/edit-user',[RegisteredUserController::class,'edit'])->name('users.edit');
        Route::get('/get-user',[RegisteredUserController::class,'getAllUser'])->name('users.data');

        Route::get('/projects',[ProjectController::class,'index'])->name('projects');
        Route::post('/save',[ProjectController::class,'store'])->name('project.store');
        Route::get('/save',[ProjectController::class,'get'])->name('project.data');
        Route::delete('/report',[UserReportController::class,'delete'])->name('user-reports.delete');

    });

    Route::post('/report',[UserReportController::class,'store'])->name('user-reports.store');
    Route::patch('/report',[UserReportController::class,'store'])->name('user-reports.update');
    Route::get('/reporting',[UserReportController::class,'index'])->name('reporting');
    Route::get('/reports',[UserReportController::class,'getData'])->name('reports.data');


});
Route::get('test',function(){
    return view('testing.test');
});

require __DIR__.'/auth.php';


Route::get('/layout',function(){
        return view('tabler.layout-vertical-1');
        return view('tabler.layout-vertical');
});
Route::get('/modal',function(){
    return view('tabler.modals');
});
