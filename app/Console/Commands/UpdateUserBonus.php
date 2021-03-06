<?php

    namespace App\Console\Commands;

    use App\Bonus;
    use App\BonusLevels;
    use App\Http\Controllers\UserController;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\DB;

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
            $startTime = microtime(true);

            $thisMonth = date('Y-m-01');
            $lastMonth = date('Y-m-01',strtotime("-1 month"));
            $bonus = Bonus::where('set_date',$lastMonth)->select(['id','erp_user_id','boundary'])->with('levels')->get();
            $erpUserDatas = new UserController();
            $erpUserDatas->getErpUser();
            $leaveUser = collect($erpUserDatas->users)->whereBetween('user_resign_date',[$lastMonth,date('Y-m-31',strtotime("-1 month"))])->pluck('id')->toArray();

            DB::beginTransaction();
            try{

                $bonus->each(function($v) use($thisMonth,$leaveUser){

                    $v->set_date = $thisMonth;

                    if(in_array($v->erp_user_id,$leaveUser)){
                        $v->boundary = 0;
                    }
                    $BonusThisDate = Bonus::where('erp_user_id',$v->erp_user_id)->where('set_date',$v->set_date);
                    if(!$BonusThisDate->exists()){
                        $bonus = Bonus::create($v->toArray());

                        if(in_array($v->erp_user_id,$leaveUser)) {
                            return;
                        }

                        collect($v->levels)->map(function ($v) use ($bonus) {
                            $v->bonus_id = $bonus->id;

                            BonusLevels::create($v->toArray());
                        });
                    }
                });

                DB::commit();

            } catch (\Exception $e)
            {
                DB::rollback();
                /*mail notice Job*/
                \App\Jobs\SentMail::dispatch('crontab',['mail'=>env('NOTIFICATION_EMAIL'),'name'=>'admin', 'title' => "{$this->signature} error {$e->getMessage()}"]);
            }

            $runTime = round(microtime(true) - $startTime, 2);
            echo ("Commands: {$this->signature} ({$runTime} seconds)\n");


        }
    }
