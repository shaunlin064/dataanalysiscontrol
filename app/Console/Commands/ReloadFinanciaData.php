<?php

namespace App\Console\Commands;

use App\FinancialList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReloadFinanciaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload_financial_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重新讀取所有財務資料';

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
	    DB::table('financial_lists')->truncate();
	    DB::table('financial_receipts')->truncate();
	    DB::table('financial_provides')->truncate();
	    $finanicalList->saveUntilNowAllData();
    }
}
