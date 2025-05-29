<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\EmailPreferenceController;
use App\Http\Controllers\Auth\RegisteredUserController;
// Route::get('/trigger-daily-email', function () {
//     Artisan::call('app:send-daily-notifications');
//     return 'Triggered';
// });
Route::get('/', function () {
    if(Auth::check()){
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
    // I want to call the dashbaord function in the UserReportController
        return redirect()->route('dashboard.show');
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

        Route::get('/show-dashboared', [UserReportController::class, 'dashboard'])->name('dashboard.show');
    });

    Route::post('/report',[UserReportController::class,'store'])->name('user-reports.store');
    Route::patch('/report',[UserReportController::class,'edit'])->name('user-reports.update');
    Route::get('/reporting',[UserReportController::class,'index'])->name('reporting');
    Route::get('/reports',[UserReportController::class,'getData'])->name('reports.data');
    Route::delete('/report/{id}', [UserReportController::class, 'destroy'])->name('report.destroy');


});
Route::get('test',function(){
    return view('emails.daily-notification', ['user' => auth()->user()]);
});

require __DIR__.'/auth.php';



Route::get('/layout',function(){
        return view('tabler.layout-vertical-1');
        return view('tabler.layout-vertical');
});
Route::get('/modal',function(){
    return view('tabler.modals');
});

// Email preference routes
Route::get('/unsubscribe/{user}', [EmailPreferenceController::class, 'unsubscribe'])->name('email.unsubscribe');
Route::post('/email-preferences/{user}', [EmailPreferenceController::class, 'update'])->name('email.preferences.update');


// /usr/local/bin/php /home/qaadxdgj/report.qaadvance.com/artisan app:send-daily-notifications >> /home/qaadxdgj/cron.log