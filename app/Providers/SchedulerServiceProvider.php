<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class SchedulerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = app(Schedule::class);


            //For testing — run every minute:
            $schedule->command('invites:delete-expired')->everyMinute();


            //Production version — run it weekly
            // $schedule->command('invites:delete-expired')->weekly();
        });
    }
}
