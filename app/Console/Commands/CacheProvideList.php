<?php

namespace App\Console\Commands;

use App\Http\Controllers\Financial\ProvideController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

        try {
            DB::beginTransaction();
               //Dosomething...
            $provideController = new ProvideController();
            $provideController->getProvideListDatas();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            // Handle Error
            \App\Jobs\SentMail::dispatch('crontab',['mail'=>env('NOTIFICATION_EMAIL'),'name'=>'admin', 'title' => "{$this->signature} error {$e->getMessage()}"]);
        }


        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}
