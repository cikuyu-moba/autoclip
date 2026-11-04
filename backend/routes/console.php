<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automatic software releases sync
Schedule::command('software:sync')->twiceDaily(1, 13)->withoutOverlapping()->runInBackground();

// Schedule automatic discovery of new software releases weekly
Schedule::command('software:discover --limit=2')->weeklyOn(1, '03:00')->withoutOverlapping()->runInBackground();
