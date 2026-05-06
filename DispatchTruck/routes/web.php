<?php

use App\Livewire\LandingPage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', LandingPage::class)->name('home');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isDispatcher()) {
            return redirect()->route('dispatcher.dashboard');
        } elseif ($user->isDriver()) {
            return redirect()->route('driver.dashboard');
        } elseif ($user->isClient()) {
            return redirect()->route('client.dashboard');
        }

        return redirect('/');
    })->name('dashboard');
});

// Include role-based module routes
require __DIR__ . '/modules/admin.php';
require __DIR__ . '/modules/dispatcher.php';
require __DIR__ . '/modules/driver.php';
require __DIR__ . '/modules/client.php';