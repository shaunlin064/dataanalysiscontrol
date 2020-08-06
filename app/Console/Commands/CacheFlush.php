<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheFlush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache_flush';

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


    public function handle()
    {
        dd(Cache::store('memcached')->flush(), Cache::store('file')->flush(), DB::table('cache_key')->truncate(),
            DB::table('cache_key_subs')->truncate(), DB::table('cache_key_cache_key_subs')->truncate());
    }
}
