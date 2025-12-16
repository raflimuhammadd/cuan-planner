<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::get('testing', fn() => Inertia::render('Testing'));


// Controller:Dashboard
Route::controller(DashboardController::class)->group(function () {
    Route::get('dashboard', 'index')->name('dashboard');
});

// Controller:Goal
Route::controller(GoalController::class)->group(function () {
    Route::get('goals', 'index')->name(name: 'goals.index');
    Route::get('goals/create', 'create')->name('goals.create');
    Route::post('goals/create', 'store')->name(name: 'goals.store');
    Route::get('goals/{goal}/edit', 'edit')->name('goals.edit');
    Route::put('goals/{goal}/edit', 'update')->name('goals.update');
    Route::delete('goals/{goal}/destroy', 'destroy')->name('goals.destroy');
});

// Controller:Balance Controller
Route::controller(BalanceController::class)->group(function () {
    Route::get('goals/{goal}/balances', 'index')->name(name: 'balances.index');
    Route::get('goals/{goal}/balances/create', 'create')->name('balances.create');
    Route::post('goals/{goal}/balances/create', 'store')->name(name: 'balances.store');
    Route::delete('goals/{goal}/balances/{balance}', 'destroy')->name('balances.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
