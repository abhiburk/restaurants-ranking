<?php

use App\Jobs\GenerateCityPulseJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
    Log::info(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new GenerateCityPulseJob)->hourly()->timezone('Asia/Kolkata');
Schedule::command('pulse:prune')->dailyAt('03:00')->timezone('Asia/Kolkata');


Schedule::command('php artisan migrate:fresh')->daily();
Schedule::command('php artisan votes:reset-daily')->daily();