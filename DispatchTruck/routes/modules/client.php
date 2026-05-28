<?php

use App\Livewire\Dashboard\ClientDashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Notifications\ClientNotificationList;
use App\Livewire\DeliveryRequest\Client\ClientRequestShow;
use App\Livewire\DeliveryRequest\Client\ClientRequestList;
use App\Livewire\DeliveryRequest\Client\ClientRequestCreate;
use App\Livewire\DeliveryRequest\Client\ClientRequestEdit;

// Client routes
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    // Dashboard
    Route::get('/dashboard', ClientDashboard::class)->name('dashboard');

    // Notifications
    Route::get('/notifications', ClientNotificationList::class)->name('notifications.index');

    Route::prefix('delivery')->name('delivery.')->group(function () {
        Route::get('/', ClientRequestList::class)->name('index');
        Route::get('/create', ClientRequestCreate::class)->name('create');
        Route::get('/{id}', ClientRequestShow::class)->name('show');
        Route::get('/{id}/edit', ClientRequestEdit::class)->name('edit');
    });

    // Settings
    Route::get('/settings', function () {
        return view('pages.admin.settings');
    })->name('settings');
}    );