<?php

use App\Livewire\Dashboard\DispatcherDashboard;
use Illuminate\Support\Facades\Route;

// Dispatcher routes
Route::middleware(['auth', 'role:dispatcher'])->prefix('dispatcher')->name('dispatcher.')->group(function () {
    // Dashboard
    Route::get('/dashboard', DispatcherDashboard::class)->name('dashboard');

    // Load Management
    Route::get('/loads', function () {
        return view('pages.dispatcher.loads');
    })->name('loads');

    // Driver Management
    Route::get('/drivers', function () {
        return view('pages.dispatcher.drivers');
    })->name('drivers');

    // Assignments
    Route::get('/assignments', function () {
        return view('pages.dispatcher.assignments');
    })->name('assignments');

    // Schedule
    Route::get('/schedule', function () {
        return view('pages.dispatcher.schedule');
    })->name('schedule');
});