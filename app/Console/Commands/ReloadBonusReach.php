<?php

namespace App\Console\Commands;


use App\Http\Controllers\Bonus\BonusReachController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReloadBonusReach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload_bonus_reach';

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
	    DB::statement('SET FOREIGN_KEY_CHECKS=0');
	    DB::table('bonus_reach')->truncate();
	    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	    $bonusReach = new BonusReachController();
	    $bonusReach->update();
    }
}
