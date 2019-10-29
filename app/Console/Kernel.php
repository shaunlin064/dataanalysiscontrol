<?php

namespace App\Console;

use App\Console\Commands\UpdateFinancialData;
use App\Console\Commands\UpdateSaleGroups;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\UpdateUserBonus;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      UpdateUserBonus::class,
      UpdateFinancialData::class,
      UpdateSaleGroups::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
	    $schedule->command('update_financial_money_receipt')->dailyAt('00:20');
	    $schedule->command('update_user_bonus')->monthlyOn('1','00:00');
	    $schedule->command('update_sale_groups')->monthlyOn('1','00:010');
	    $schedule->command('update_financial_data')->monthlyOn('16','00:00');
	    $schedule->command('update_bonus_reach')->monthlyOn('16','00:10');
	    $schedule->command('update_convener_reach')->monthlyOn('16','00:20');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    
    protected function scheduleTimezone()
    {
        return 'Asia/Taipei';
    }
}
