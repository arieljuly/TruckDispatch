<?php

use App\Livewire\Dashboard\DriverDashboard;
use App\Livewire\Maintenance\Driver\Maintenance;
use App\Livewire\Notifications\DriverNotificationList;
use App\Livewire\TruckManagement\AssignedTruck;
use Illuminate\Support\Facades\Route;

// Driver routes
Route::middleware(['auth', 'role:driver'])->prefix('driver')->name('driver.')->group(function () {
    // Dashboard
    Route::get('/dashboard', DriverDashboard::class)->name('dashboard');

    //maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', Maintenance::class)->name('index');
    });

    //trucks
    Route::prefix('assigned-trucks')->name('trucks.')->group(function () {
        Route::get('/', AssignedTruck::class)->name('assigned');
    });

    //notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', DriverNotificationList::class)->name('index');
    });

    // Settings
    Route::get('/settings', function () {
        return view('pages.admin.settings');
    })->name('settings');
});