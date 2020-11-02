<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class CheckAddNewFinancialUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check_add_new_financial_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '檢查是否有未在bonus名單內的user，先暫時新增至後勤團隊內(基本上應該是後勤人員,業務在剛入職會手動設定)';

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
     * @return int
     */
    public function handle()
    {
	    $startTime = microtime(true);
	    $bounusErpUserId = \App\Bonus::all()->pluck('erp_user_id')->unique()->values();
	    $newErpUserIds = \App\FinancialList::whereNotIn('erp_user_id',$bounusErpUserId)->get()->groupBy('erp_user_id');
	
	    $newErpUserIds->each(function($datas,$erpUserId){
		    $setDates = $datas->pluck('set_date')->unique()->values();
		    $setDates->each(function($setDate) use($erpUserId){
			    $bonus = \App\Bonus::create(
				    [
					    'erp_user_id' => $erpUserId,
					    'set_date' => $setDate,
					    'boundary' => 0,
				    ]
			    );
			    $bonus->levels()->create([
				    'achieving_rate' => 100,
				    'bonus_rate' => 0,
				    'bonus_direct' => 0,
			    ]);
			    $saleGroup = \App\SaleGroups::find(4);
			    $saleGroup->groupsUsers()->create([
				    'erp_user_id' => $erpUserId,
				    'set_date' => $setDate,
			    ]);
		    });
	    });
	    if($newErpUserIds->isNotEmpty()){
		    $user = new User();
            $user->syncUserDataFromErp();
	    }
	    
	    $runTime = round(microtime(true) - $startTime, 2);
	    echo ("Commands: {$this->signature} ({$runTime} seconds)\n");
    }
}
