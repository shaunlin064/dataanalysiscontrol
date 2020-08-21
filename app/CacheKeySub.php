<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CacheKeySub extends Model
{
    //
    protected $fillable = ['type','key','condition','use_times' ];
    protected $attributes = [
        'use_times'            => 0,
    ];
    public function cacheKey (  ) {
        return $this->belongsToMany(Cachekey::Class,'cache_key_cache_key_subs','cache_key_id','cache_key_subs_id');
    }

    public function getCacheData () {
        if($this->key){
            $this->use_times += 1;
            $this->update();
            return Cache::get($this->key);
        }
    }

}
