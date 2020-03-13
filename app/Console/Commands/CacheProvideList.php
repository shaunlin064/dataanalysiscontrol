<?php

namespace App\Console\Commands;

use App\Http\Controllers\Financial\ProvideController;
use App\SaleGroups;
use Illuminate\Http\Request;
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
    protected $description = '快取獎金發放資料';

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

        $date = new \DateTime();
        $dateStart =  $date->format('2018-12-01');
        $dateEnd = $date->format('Y-m-01');
        $ObjSaleGroups = new SaleGroups();
        $saleGroupsIds = $ObjSaleGroups->all()->pluck('id')->toArray();
        $request = new Request(['startDate' => $dateStart,'endDate'=>$dateEnd,'saleGroupIds' => $saleGroupsIds,'userIds'=>[]]);
        $provideObj = new ProvideController();


        $provideObj->getAjaxProvideData($request);

        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}
