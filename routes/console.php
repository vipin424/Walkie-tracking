<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:order-reminder')->dailyAt('09:00'); // Runs daily at 9 AM

// Send monthly invoices automatically on billing day at 9 AM
Schedule::command('invoices:send-monthly')->dailyAt('09:00');

// Send payment reminders for unpaid invoices (7 days after invoice sent) at 10 AM
Schedule::command('invoices:send-reminders')->dailyAt('10:00');
