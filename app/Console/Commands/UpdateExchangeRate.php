<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateExchangeRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_exchange_rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新外幣匯率從台灣銀行取得牌價';

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
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        //
        exec('python python/getExchangeRate.py');
        exec('chmod 777 -R storage/app/public/exchangeRate.txt');

        if(!Storage::exists('/public/exchangeRate.txt')){
            /*mail notice Job*/
            \App\Jobs\SentMail::dispatch('crontab',['mail'=>env('NOTIFICATION_EMAIL'),'name'=>'admin', 'title' => "{$this->signature} error error /public/exchangeRate.txt not exists" ]);
        }else{
            $data = json_decode(Storage::get('/public/exchangeRate.txt'));
            $rates = new \App\ExchangeRatesAll();
            try {
                DB::beginTransaction();
                    $rates->saveData($data);
                DB::commit();
                exec('rm -rf storage/app/public/exchangeRate.txt');
            } catch(\Exception $e) {
                DB::rollback();
                // Handle Error
                /*mail notice Job*/
                \App\Jobs\SentMail::dispatch('crontab',['mail'=>env('NOTIFICATION_EMAIL'),'name'=>'admin', 'title' => "{$this->signature} error {$e->getMessage()}"]);
            }

        }
    }
}
