<?php

    namespace App\Console;

    use App\Console\Commands\CacheAll;
    use App\Console\Commands\CacheFinancialList;
    use App\Console\Commands\CacheReceiptTimes;
    use App\Console\Commands\CheckAddNewFinancialUser;
    use App\Console\Commands\UpdateBonusReach;
    use App\Console\Commands\UpdateConvenerReach;
    use App\Console\Commands\UpdateExchangeRate;
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
            UpdateExchangeRate::class,
            CheckAddNewFinancialUser::class,
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
                $schedule->command('update_financial_data')->twiceDaily(12, 18);
                $schedule->command('update_financial_data')->daily();
                
                $schedule->command('update_exchange_rate')->daily();
                
	            $schedule->command('check_add_new_financial_user')->daily();
	            
                $schedule->command('update_user_bonus')->monthlyOn('1', '00:00');
                $schedule->command('update_sale_groups')->monthlyOn('1', '00:10');
                $schedule->command('update_bonus_reach')->monthlyOn('16', '00:10');
                $schedule->command('update_convener_reach')->monthlyOn('16', '00:20');
                
                /*telescope*/
                $schedule->command('telescope:prune')->daily();
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
