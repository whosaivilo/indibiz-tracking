<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mengatur scheduler cron job agar jalan otomatis (misal: setiap jam)
Schedule::command('import:wo-excel batam')->hourly();
Schedule::command('import:wo-excel sumbar')->hourly();
Schedule::command('import:wo-excel ridar')->hourly();
