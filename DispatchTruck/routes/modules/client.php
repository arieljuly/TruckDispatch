<?php

use App\Livewire\Dashboard\ClientDashboard;
use Illuminate\Support\Facades\Route;

// Client routes
Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    // Dashboard
    Route::get('/dashboard', ClientDashboard::class)->name('dashboard');

    // Shipments Management
    Route::get('/shipments', function () {
        return view('pages.client.shipments');
    })->name('shipments');

    Route::get('/shipments/create', function () {
        return view('pages.client.create-shipment');
    })->name('create-shipment');

    // Invoices
    Route::get('/invoices', function () {
        return view('pages.client.invoices');
    })->name('invoices');

    // Support
    Route::get('/support', function () {
        return view('pages.client.support');
    })->name('support');
});