<?php

namespace App\Console\Commands;

use App\Http\Controllers\Financial\ProvideController;
use Illuminate\Console\Command;

class CacheProvideList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache_provide_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '快取發放獎金清單';

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

        $provideController = new ProvideController();
        $provideController->getProvideListDatas();

        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}
