<?php


    namespace App\Console\Commands;

    use App\Cachekey;
    use App\Http\Controllers\Bonus\ReviewController;
    use App\SaleGroups;
    use Illuminate\Console\Command;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    class CacheFinancialList extends Command {

        protected $signature = 'cache_financial_list';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'cache_financial_list';

        /**
         * Create a new job instance.
         *
         * @return void
         */
        public function __construct()
        {
            //
            parent::__construct();
        }

        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function handle () {
            //
            try {
                DB::beginTransaction();
                   //Dosomething...
                $startTime = microtime(true);

                $date = new \DateTime();
                $dateStart = $date->format('2018-01-01');
                $dateEnd = $date->format('Y-m-01');
                $ObjSaleGroups = new SaleGroups();
                $saleGroupsIds = $ObjSaleGroups->all()->pluck('id')->map(function ( $v ) {
                    return (string) $v;
                })->toArray();
                /*cache start*/
                $dateRange = date_range($dateStart, $dateEnd);
                $dateRange[] = $dateEnd;
                $reviewObj = new ReviewController();
                $cacheObj = Cachekey::where('type', 'financial.review')->get();

                foreach ( $dateRange as $date ) {

                    if ( $cacheObj->where('set_date', $date)->count() == 0 ) {
                        $request = new Request([
                            'startDate'              => $date,
                            'endDate'                => $date,
                            'saleGroupIds'           => $saleGroupsIds,
                            'userIds'                => [],
                            'agencyIdArrays'         => [],
                            'clientIdArrays'         => [],
                            'mediaCompaniesIdArrays' => [],
                            'mediasNameArrays'       => []
                        ]);
                        $reviewObj->getCacheDatas($request);
                        $runTime = round(microtime(true) - $startTime, 2);
                        echo( "Commands: {$this->signature} {$date} ({$runTime} seconds)\n" );
                    }
                }

                $runTime = round(microtime(true) - $startTime, 2);
                echo( "Commands: {$this->signature} ({$runTime} seconds)\n" );
                DB::commit();
            } catch(\Exception $e) {
                DB::rollback();
                // Handle Error
                \App\Jobs\SentMail::dispatch('crontab',['mail'=>env('NOTIFICATION_EMAIL'),'name'=>'admin', 'title' => "{$this->signature} error {$e->getMessage()}"]);
            }

        }
    }
