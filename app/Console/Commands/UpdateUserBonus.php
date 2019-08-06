<?php

namespace App\Console\Commands;

use App\Bonus;
use App\BonusLevels;
use Illuminate\Console\Command;

class UpdateUserBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_user_bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每月新增user新月份責任額';

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
	    $thisMonth = date('Y-m-01');
	    $lastMonth = date('Y-m-01',strtotime("-1 month"));
	    $bonus = Bonus::where('set_date',$lastMonth)->select(['id','user_id','boundary'])->with('levels')->get();
	    
	    DB::beginTransaction();
	    try{
		
		    $bonus->each(function($v) use($thisMonth){
			
			    $v->set_date = $thisMonth;
			    $v = $v->toArray();
			
			    $bonus = Bonus::create($v);
			
			    collect($v['levels'])->map(function($v) use($bonus){
				    $v['bonus_id'] = $bonus->id;
				    BonusLevels::create($v);
			    });
			
		    });
		
		    DB::commit();
		
	    } catch (\Exception $ex) {
		    DB::rollback();
		    \Log::error($ex->getMessage());
	    }
	    
    }
}