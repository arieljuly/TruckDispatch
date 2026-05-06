<?php

use App\Livewire\Dashboard\DriverDashboard;
use Illuminate\Support\Facades\Route;

// Driver routes
Route::middleware(['auth', 'role:driver'])->prefix('driver')->name('driver.')->group(function () {
    // Dashboard
    Route::get('/dashboard', DriverDashboard::class)->name('dashboard');

    // Trips Management
    Route::get('/trips', function () {
        return view('pages.driver.trips');
    })->name('trips');

    // Documents
    Route::get('/documents', function () {
        return view('pages.driver.documents');
    })->name('documents');

    // Payments
    Route::get('/payments', function () {
        return view('pages.driver.payments');
    })->name('payments');

    // Messages
    Route::get('/messages', function () {
        return view('pages.driver.messages');
    })->name('messages');
});