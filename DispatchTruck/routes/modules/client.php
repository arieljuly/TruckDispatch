<?php

use App\Livewire\Dashboard\ClientDashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Notifications\ClientNotificationList;
use App\Livewire\DeliveryRequest\Client\ClientRequestShow;

// Client routes
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    // Dashboard
    Route::get('/dashboard', ClientDashboard::class)->name('dashboard');

    // Notifications
    Route::get('/notifications', ClientNotificationList::class)->name('notifications.index');

    // Delivery
    Route::get('/delivery', ClientRequestShow::class)->name('delivery.index');

    // Settings
    Route::get('/settings', function () {
        return view('pages.admin.settings');
    })->name('settings');
}    );