<?php

namespace App\Console\Commands;

use App\FinancialList;
use Illuminate\Console\Command;

class SetOldCampaignOwn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set_old_campaign_own';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重設系統啟用前 離職人員案件交接 正確月份資料';

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
        
	    $changeCampaigns = [
	     [
	      'id'=>271,
	      'beforeDate' => '2019-02-01',
		     'ownId' => 96
	     ],
	     [
		    'id'=>1285,
		    'beforeDate' => '2019-04-01',
		    'ownId' => 107
	     ],
	     [
		    'id'=>1051,
		    'beforeDate' => '2019-04-01',
		    'ownId' => 107
	     ],
	     [
		    'id'=>663,
		    'beforeDate' => '2019-04-01',
		    'ownId' => 107
	     ],
	     [
		    'id'=>1276,
		    'beforeDate' => '2019-04-01',
		    'ownId' => 107
	     ],
	     [
		    'id'=>528,
		    'beforeDate' => '2019-02-01',
		    'ownId' => 96
	     ],
	     [
		    'id'=>149,
		    'beforeDate' => '2019-02-01',
		    'ownId' => 96
	     ],
	    ];
	    foreach ($changeCampaigns as $changeCampaign){
		    $financialData = FinancialList::where('campaign_id',$changeCampaign['id'])->where('set_date','<',$changeCampaign['beforeDate']);
		    
		    if($financialData->exists()){
			    $financialData->get()->map(function($v,$k) use ( $changeCampaign ) {
				    $v->erp_user_id = $changeCampaign['ownId'];
			      $v->update();
			    });
		    }
	    }
	    
        $runTime = round(microtime(true) - $startTime, 2);
        echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}
