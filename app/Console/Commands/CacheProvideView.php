<?php

namespace App\Console\Commands;

use App\Cachekey;
use App\Http\Controllers\Financial\ProvideController;
use App\SaleGroups;
use Illuminate\Http\Request;
use Illuminate\Console\Command;

class CacheProvideView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache_provide_view';

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
        $dateStart = $date->format('2018-01-01');
        $dateEnd = $date->format('Y-m-01');
        $dateRange = date_range($dateStart, $dateEnd);
        $dateRange[] = $dateEnd;

        $ObjSaleGroups = new SaleGroups();
        $saleGroupsIds = $ObjSaleGroups->all()->pluck('id')->toArray();

        $cacheObj = Cachekey::where('type', 'provide.view')->get();
        foreach($dateRange as $date){
            if ( $cacheObj->where('set_date', $date)->count() == 0) {
                $request = new Request([
                        'startDate'    => $date,
                        'endDate'      => $date,
                        'saleGroupIds' => $saleGroupsIds,
                        'userIds'      => []
                    ]);
                $provideObj = new ProvideController();
                $provideObj->getAjaxProvideData($request, 'return');

                $runTime = round(microtime(true) - $startTime, 2);
                echo( "Commands: {$this->signature} {$date} ({$runTime} seconds)\n" );
            }
        }


        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}
