<?php
    
    namespace App\Console;
    
    use App\Console\Commands\CacheAll;
    use App\Console\Commands\CacheFinancialList;
    use App\Console\Commands\CacheReceiptTimes;
    use App\Console\Commands\UpdateBonusReach;
    use App\Console\Commands\UpdateConvenerReach;
    use App\Console\Commands\UpdateFinancialData;
    use App\Console\Commands\UpdateFinancialMoneyReceipt;
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
            UpdateFinancialMoneyReceipt::class,
            UpdateUserBonus::class,
            UpdateFinancialData::class,
            UpdateSaleGroups::class,
            UpdateFinancialData::class,
            UpdateBonusReach::class,
            UpdateConvenerReach::class,
            CacheAll::class,
        ];
        
        /**
         * Define the application's command schedule.
         *
         * @param \Illuminate\Console\Scheduling\Schedule $schedule
         * @return void
         */
        protected function schedule (Schedule $schedule)
        {
            if(env('APP_ENV') != 'local'){
                $schedule->command('update_financial_data')->twiceDaily(10, 15);
                $schedule->command('update_financial_money_receipt')->dailyAt('00:20');
                $schedule->command('update_user_bonus')->monthlyOn('1', '00:00');
                $schedule->command('update_sale_groups')->monthlyOn('1', '00:10');
                $schedule->command('update_bonus_reach')->monthlyOn('16', '00:10');
                $schedule->command('update_convener_reach')->monthlyOn('16', '00:20');
                /*cached*/
                $schedule->command('cache_all')->dailyAt('01:00');
                $schedule->command('cache_all')->twiceDaily(11, 16);
                $schedule->command('cache_clean')->monthlyOn('1', '00:30');
                /*telescope*/
                $schedule->command('telescope:prune --hours=48')->daily();
            }
        }
        
        /**
         * Register the commands for the application.
         *
         * @return void
         */
        protected function commands ()
        {
            $this->load(__DIR__ . '/Commands');
            
            require base_path('routes/console.php');
        }
        
        protected function scheduleTimezone ()
        {
            return 'Asia/Taipei';
        }
    }
