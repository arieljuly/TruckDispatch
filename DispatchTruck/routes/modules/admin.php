<?php

use App\Livewire\AreaManagement\AreaList;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Admin\UserManagement\UserShow;
use App\Livewire\Admin\UserManagement\UserEdit;
use App\Livewire\DispatchManagement\AllocationCreate;
use App\Livewire\DispatchManagement\AllocationList;
use App\Livewire\DispatchManagement\AssignmentList;
use App\Livewire\DispatchManagement\AssignmentShow;
use App\Livewire\DispatchManagement\CreateDispatch;
use App\Livewire\DispatchManagement\DispatchHistory;
use App\Livewire\DispatchManagement\DispatchShow;
use App\Livewire\TruckLogs\TruckLogShow;
use App\Livewire\TruckManagement\FuelCreate;
use App\Livewire\TruckManagement\FuelEdit;
use App\Livewire\TruckManagement\FuelList;
use App\Livewire\TruckManagement\FuelManagement;
use App\Livewire\TruckManagement\TruckCompartments;
use App\Livewire\TruckManagement\TruckList; 
use App\Livewire\TruckManagement\TruckCreate;
use App\Livewire\TruckManagement\TruckEdit;
use App\Livewire\TruckManagement\TruckShow;
use App\Livewire\Maintenance\MaintenanceShow;
use App\Livewire\Maintenance\MaintenanceCreate;
use App\Livewire\Maintenance\MaintenanceEdit;
use App\Livewire\DeliveryRequest\RequestDelivery;
use App\Livewire\DriverManagement\DriverShow;
use App\Livewire\DriverManagement\DriverList;
use App\Livewire\DriverManagement\DriverEdit;
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
    });

    // Driver Managemnt
    Route::prefix('drivers')->name('drivers.')->group(function () {
        Route::get('/', DriverList::class)->name('index');
        Route::get('/{id}', DriverShow::class)->name('show');
        Route::get('/{id}/edit', DriverEdit::class)->name('edit');

    });
    // Area Management
    Route::prefix('areas')->name('areas.')->group(function () {
        Route::get('/', AreaList::class)->name('index');
        Route::get('/create', AreaCreate::class)->name('create');
        Route::get('/{id}/edit', AreaEdit::class)->name('edit');
        Route::get('/{id}', AreaShow::class)->name('show');
        Route::get('/demand/summary', AreaDemand::class)->name('demand');
    });

    Route::prefix('trucks')->name('trucks.')->group(function () {
        Route::get('/', TruckList::class)->name('index');
        Route::get('/create', TruckCreate::class)->name('create');
        Route::get('/compartments', TruckCompartments::class)->name('compartments');              // ✅ Before /{id}
        Route::get('/{id}', TruckShow::class)->name('show');                        // Dynamic last
        Route::get('/{id}/edit', TruckEdit::class)->name('edit');
    });
    Route::prefix('fuel')->name('fuel.')->group(function () {
        Route::get('/', FuelManagement::class)->name('index');
        Route::get('/list', FuelList::class)->name('list');
        Route::get('/create', FuelCreate::class)->name('create');
        Route::get('/{id}/edit', FuelEdit::class)->name('edit');
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

    Route::prefix('dispatch')->name('dispatch.')->group(function () {
        Route::get('/', DispatchHistory::class)->name('index');

        Route::get('/create', CreateDispatch::class)->name('create');

        // View specific dispatch session
        Route::get('/sessions/{id}', DispatchShow::class)->name('show');

        // Allocations (view only)
        Route::get('/allocations', AllocationList::class)->name('allocations');

        // Assignments
        Route::get('/assignments', AssignmentList::class)->name('assignments');
        Route::get('/assignments/{id}', AssignmentShow::class)->name('assignment.show');
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