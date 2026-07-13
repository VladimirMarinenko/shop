<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Определите команды, которые должны выполняться по расписанию.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('telegram:import 5')->everyThirtyMinutes();
    }

    /**
     * Зарегистрируйте команды для artisan.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
