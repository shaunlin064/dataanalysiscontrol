<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

ini_set('max_execution_time', 600);
ini_set('memory_limit','1024M');

class CacheAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache_all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $startTime = microtime(true);
        Artisan::call('down');
        Artisan::call('cache_clean');
        Artisan::call('cache_financial_list');
        Artisan::call('cache_receipt_times');
        Artisan::call('cache_provide_list');
        Artisan::call('up');
        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}
