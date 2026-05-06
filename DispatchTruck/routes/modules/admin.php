<?php

use App\Livewire\Dashboard\AdminDashboard;
use Illuminate\Support\Facades\Route;

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

    // User Management
    Route::get('/users', function () {
        return view('pages.admin.users');
    })->name('users');

    // Fleet Management
    Route::get('/fleets', function () {
        return view('pages.admin.fleets');
    })->name('fleets');

    // Reports
    Route::get('/reports', function () {
        return view('pages.admin.reports');
    })->name('reports');

    // Settings
    Route::get('/settings', function () {
        return view('pages.admin.settings');
    })->name('settings');
});