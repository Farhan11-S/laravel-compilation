<?php

namespace App\Console;

use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $dynamicCommands = Setting::where('name', 'LIKE', '%dynamic-command:%')
            ->where('name', 'NOT LIKE', '%-limit')
            ->get();

        foreach ($dynamicCommands as $command) {
            $schedule->command($command->name)->cron($command->value);
        }

        $schedule->command('subscription-transaction:expired')->everyThreeHours();
        $schedule->command('subscription-transaction:ended')->daily();
        $schedule->command('coupon-usage:ended')->daily();
        $schedule->command('app:publish-job')->everyTwoHours();
        $schedule->command('app:bulk-post-to-social-media')->everyFifteenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
