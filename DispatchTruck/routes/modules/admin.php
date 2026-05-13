<?php

use App\Livewire\AreaManagement\AreaList;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Admin\UserManagement\UserShow;
use App\Livewire\Admin\UserManagement\UserEdit;
use App\Livewire\TruckLogs\TruckLogShow;
use App\Livewire\TruckManagement\TruckShow;
use App\Livewire\TruckManagement\TruckCreate;
use App\Livewire\TruckManagement\TruckEdit;
use App\Livewire\Maintenance\MaintenanceShow;
use App\Livewire\Maintenance\MaintenanceCreate;
use App\Livewire\Maintenance\MaintenanceEdit;
use App\Livewire\DeliveryRequest\RequestDelivery;
use App\Livewire\DriverManagement\DriverShow;
use App\Livewire\AreaManagement\AreaShow;
use App\Livewire\AreaManagement\AreaCreate;
use App\Livewire\AreaManagement\AreaEdit;
use App\Livewire\AreaManagement\AreaDemand;
use App\Livewire\Reports\Reports;
use App\Livewire\Notifications\NotificationList;
use Illuminate\Support\Facades\Route;

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', UserShow::class)->name('index');

        //for the driver
        Route::get('/drivers', DriverShow::class)->name('drivers');
    });

    // Area Management
    Route::prefix('areas')->name('areas.')->group(function () {
        Route::get('/', AreaList::class)->name('index');
        Route::get('/create', AreaCreate::class)->name('create');
        Route::get('/{id}/edit', AreaEdit::class)->name('edit');
        Route::get('/{id}', AreaShow::class)->name('show');
        Route::get('/demand/summary', AreaDemand::class)->name('demand');
    });

    // Truck Management
    Route::prefix('trucks')->name('trucks.')->group(function () {
        Route::get('/', TruckShow::class)->name('index');
        Route::get('/create', TruckCreate::class)->name('create');
        Route::get('/{id}/edit', TruckEdit::class)->name('edit');
    });

    // Maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', MaintenanceShow::class)->name('index');
        Route::get('/create', MaintenanceCreate::class)->name('create');
        Route::get('/{id}/edit', MaintenanceEdit::class)->name('edit');
    });

    // Truck Logs
    Route::prefix('truck-logs')->name('truck-logs.')->group(function () {
        Route::get('/', TruckLogShow::class)->name('index');

    });

    //Dispatch Management
    Route::prefix('dispatch')->name('dispatch.')->group(function () {
        Route::get('/', TruckLogShow::class)->name('index');
    });
    //Delivery Requests
    Route::prefix('delivery-requests')->name('delivery-requests.')->group(function () {
        Route::get('/', RequestDelivery::class)->name('index');
    });

    // Reports
    Route::get('/reports', Reports::class)->name('reports');

    Route::get('/notifications', NotificationList::class)->name('notifications');

    // Settings
    Route::get('/settings', function () {
        return view('pages.admin.settings');
    })->name('settings');
});