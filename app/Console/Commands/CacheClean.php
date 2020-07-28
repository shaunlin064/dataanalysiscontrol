<?php

namespace App\Console\Commands;

use App\Cachekey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache_clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除快取';

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
        Cache::store('memcached')->flush();

        $cacheKey = new Cachekey();
        $cacheKey->releaseCache();
    }
}
