<?php

namespace App\Console\Commands;

use App\FinancialList;
use Illuminate\Console\Command;

class UpdateFinancialData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_financial_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每月更新業務財報資料';

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
	    $finanicalList = new FinancialList();
	
	    $finanicalList->saveCloseData();
	    
    }
}
