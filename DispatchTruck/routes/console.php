<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==================== ML TRAINING SCHEDULES ====================

// Weekly model retraining (every Monday at 2 AM) - ONLY ONE of these
Schedule::command('ml:export-training --send-to-api')
    ->weekly()
    ->mondays()
    ->at('02:00')
    ->description('Retrain ML model with new dispatch data');

// Daily data export backup (every day at 3 AM)
Schedule::command('ml:export-training --days=7')
    ->daily()
    ->at('03:00')
    ->description('Export weekly training data backup');

// ==================== HEALTH CHECKS ====================

// Check ML API health every hour
Schedule::command('ml:check-health')
    ->hourly()
    ->description('Check ML API health status');

// ==================== CLEANUP TASKS ====================

// Clean up old training data exports (keep last 30 days)
Schedule::command('ml:cleanup-old-exports --days=30')
    ->daily()
    ->at('04:00')
    ->description('Clean up old training data exports');