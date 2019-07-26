<?php

namespace App\Console;

use App\Bonus;
use App\BonusLevels;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
	    $schedule->call(function () {
		    $thisMonth = date('Y-m-01');
		    $lastMonth = date('Y-m-01',strtotime("-1 month"));
		    $bonus = Bonus::where('set_date',$lastMonth)->select(['id','user_id','boundary'])->with('levels')->get();
		    $bonus->each(function($v) use($thisMonth){
			
			    $v->set_date = $thisMonth;
			    $v = $v->toArray();
			
			    $bonus = Bonus::create($v);
			
			    collect($v['levels'])->map(function($v) use($bonus){
				    $v['bonus_id'] = $bonus->id;
				    BonusLevels::create($v);
			    });
			
		    });
	    })->monthlyOn('1','00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
