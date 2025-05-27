<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        
        $schedule->command('app:send-daily-notifications')
                ->dailyAt('15:10') // Server time is EDT, 9 hours behind Pakistan
                ->weekdays() // This automatically excludes Saturday and Sunday
                ->appendOutputTo(storage_path('logs/daily-notifications.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
