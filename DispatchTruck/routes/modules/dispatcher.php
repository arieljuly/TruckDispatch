<?php

use App\Livewire\Dashboard\DispatcherDashboard;
use App\Livewire\DeliveryRequest\Dispatcher\DeliveryRequests;
use App\Livewire\DispatchManagement\Dispatcher\DispatchManagement;
use App\Livewire\Maintenance\Dispatcher\Maintenance;
use App\Livewire\Notifications\DispatcherNotificationsList;
use App\Livewire\Reports\DispatcherReports;
use App\Livewire\TruckLogs\DispatcherTruckLogs;
use App\Livewire\TruckManagement\DispatchTruck;
use App\Livewire\DriverManagement\Dispatcher\DriverManagement;
use App\Livewire\DeliveryRequest\Dispatcher\DeliveryRequest;
use App\Models\TruckLog;
use Illuminate\Support\Facades\Route;

// Dispatcher routes
Route::middleware(['auth', 'role:dispatcher'])->prefix('dispatcher')->name('dispatcher.')->group(function () {
    // Dashboard
    Route::get('/dashboard', DispatcherDashboard::class)->name('dashboard');

    // Dispatch Management
    Route::prefix ('dispatch')->name('dispatch.')->group(function () {
        Route::get('/', DispatchManagement::class)->name('index');
    });

    //trucks
    Route::prefix('trucks')->name('trucks.')->group(function () {
        Route::get('/', DispatchTruck::class)->name('trucks');
        Route::get('/logs', DispatcherTruckLogs::class)->name('logs');
    });

    //drivers
    Route::prefix('drivers')->name('drivers.')->group(function () {
        Route::get('/', DriverManagement::class)->name('index');
    });

    //delivery request
    Route::prefix('delivery-requests')->name('delivery-requests.')->group(function () {
        Route::get('/delivery', DeliveryRequests::class)->name('index');
    });

    //maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', Maintenance::class)->name('index');
    });

    //reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', DispatcherReports::class)->name('index');   
    });

    //notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', DispatcherNotificationsList::class)->name('index');
    });

    // Settings
    Route::get('/settings', function () {
        return view('pages.admin.settings');
    })->name('settings');
});