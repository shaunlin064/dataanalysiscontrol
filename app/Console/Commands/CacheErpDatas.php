<?php

namespace App\Console\Commands;

use App\Cachekey;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UserController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheErpDatas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache_erp_datas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '快取後台資料';

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
        try {
            DB::beginTransaction();
               //Dosomething...
            $userObj = new UserController();
            $userObj->getErpUser();

            if(isset($userObj->users)){

                Cache::forever('department', $userObj->department);
                Cache::forever('users', $userObj->users);
                exec(sprintf('chown root:www-data -R %s/*', storage_path()));
                exec(sprintf('chmod 775 -R %s/*', storage_path()));

                foreach (['users','department'] as $item){
                    $cacheObj = CacheKey::where('type',$item);

                    if($cacheObj->exists()){
                        $cacheObj->update([
                            'set_date' => now()->format('Y-m-d'),
                            'release_time' => now()->addHour(1)->format('Y-m-d H:i:s')
                        ]);
                    }else{
                        $cacheObj = new Cachekey();
                        $cacheObj->key = $item;
                        $cacheObj->type = $item;
                        $cacheObj->set_date = now()->format('Y-m-d');
                        $cacheObj->release_time = now()->addHours(1)->format('Y-m-d H:i:s');
                        $cacheObj->save();
                    }
                }
            }
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            // Handle Error
            \App\Jobs\SentMail::dispatch('crontab',['mail'=>env('NOTIFICATION_EMAIL'),'name'=>'admin', 'title' => "{$this->signature} error {$e->getMessage()}"]);
        }

    }
}
