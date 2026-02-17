<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Compliance document expiry checks — daily at 8 AM
Schedule::command('compliance:check-expiring')->dailyAt('08:00');

// CPD reminders — weekly on Monday at 9 AM
Schedule::command('cpd:send-reminders')->weeklyOn(1, '09:00');

// Deliverable deadline reminders — daily at 8:30 AM
Schedule::command('projects:check-deliverables')->dailyAt('08:30');
