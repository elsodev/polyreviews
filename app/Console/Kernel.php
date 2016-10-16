<?php

namespace App\Console;

use App\Console\Commands\FacebookFetcher;
use App\Console\Commands\FetchStatesAreasMY;
use App\Console\Commands\GoogleScrap;
use App\Console\Commands\FoursquareFetcher;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        FetchStatesAreasMY::class,
        GoogleScrap::class,
        FoursquareFetcher::class,
        FacebookFetcher::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
