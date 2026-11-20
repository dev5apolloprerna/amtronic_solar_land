<?php

use Illuminate\Support\Facades\Schedule;

// Run `php artisan schedule:work` (or a cron calling `schedule:run` every minute) in production.
Schedule::command('solar:expire-agreements')->dailyAt('00:15');
