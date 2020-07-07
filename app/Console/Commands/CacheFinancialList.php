<?php

namespace App\Console\Commands;

use App\Http\Controllers\Bonus\ReviewController;
use App\SaleGroups;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class CacheFinancialList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache_financial_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cache_financial_list';

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
        $dateStart =  $date->format('2018-01-01');
        $dateEnd = $date->format('Y-m-01');
        $ObjSaleGroups = new SaleGroups();
        $saleGroupsIds = $ObjSaleGroups->all()->pluck('id')->map(function($v){
            return (string)$v;
        })->toArray();
        /*cache start*/
        $dateRange = date_range($dateStart, $dateEnd);
        $dateRange[] = $dateEnd;
        $reviewObj = new ReviewController();

        foreach($dateRange as $date){
            $request = new Request([
                'startDate'    => $date,
                'endDate'      => $date,
                'saleGroupIds' => $saleGroupsIds,
                'userIds'      => []
            ]);
            $reviewObj->getAjaxData($request,'none');
        }

        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}
