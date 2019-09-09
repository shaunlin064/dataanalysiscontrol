<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetReload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set_reload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重啟所有reload';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
	    Artisan::call('reload_financial_data');
	    Artisan::call('reload_financial_money_receipt');
	    Artisan::call('reload_bonus_reach');
	    Artisan::call('reload_convener_reach');
    }
}
