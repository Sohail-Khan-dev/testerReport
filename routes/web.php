<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserReportController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/report',[UserReportController::class,'store'])->name('user-reports.store');
    Route::get('/report',[UserReportController::class,'store'])->name('user-reports.get');
    Route::delete('/report',[UserReportController::class,'store'])->name('user-reports.delete');
    Route::patch('/report',[UserReportController::class,'store'])->name('user-reports.update');
    Route::get('/reporting',[UserReportController::class,'showReporting'])->name('reporting');
});

require __DIR__.'/auth.php';
