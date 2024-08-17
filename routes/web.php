<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if(Auth::check()){
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('qareport.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('register/new', [RegisteredUserController::class, 'storeNew'])->name('register.new');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/report',[UserReportController::class,'store'])->name('user-reports.store');
//    Route::get('/report',[UserReportController::class,'store'])->name('user-reports.get');
    Route::delete('/report',[UserReportController::class,'store'])->name('user-reports.delete');
    Route::patch('/report',[UserReportController::class,'store'])->name('user-reports.update');
    Route::get('/reporting',[UserReportController::class,'index'])->name('reporting');
    Route::get('/reports',[UserReportController::class,'getData'])->name('reports.data');
    // Below is for the User  Routes We have to Update the profile routes and Use this for User
    Route::get('/users',[RegisteredUserController::class,'index'])->name('users');
    Route::get('/get-user',[RegisteredUserController::class,'getAllUser'])->name('users.data');
    Route::get('/projects',[\App\Http\Controllers\ProjectController::class,'index'])->name('projects');

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
