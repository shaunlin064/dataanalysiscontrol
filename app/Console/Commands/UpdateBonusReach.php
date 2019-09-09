<?php

namespace App\Console\Commands;

use App\Http\Controllers\Bonus\BonusReachController;
use Illuminate\Console\Command;

class UpdateBonusReach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_bonus_reach';

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
	    $bonusReach = new BonusReachController();
	    $date_now =  new \DateTime();
	    $date_now->modify('-1Month');
	    
	    $bonusReach->update($date_now->format('Y-m-01'));
    }
}
