<?php


    namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Cache;

    class cachekey extends model {
        //
        protected $table = 'cache_key';
        protected $fillable = [ 'type', 'key', 'set_date', 'release_time', 'use_times' ];
        protected $attributes = [
            'use_times' => 0,
        ];

        public function __construct () {
            parent::__construct();
        }

        public function cacheKeySub () {
            return $this->belongsToMany(CacheKeySub::class, 'cache_key_cache_key_subs', 'cache_key_id',
                'cache_key_subs_id');
        }

        public function remember ( $key, $time, $callback ) {
            return Cache::store('file')->remember($key, $time, $callback);
        }

        public function put ( $cahcekey, $date, $data, $cachetime ) {

            $md5Key = md5($cahcekey . $date);

            Cache::store('file')->forever($md5Key, $data);

            if ( !empty(CacheKey::where('key', $md5Key)->first()) ) {
                $tmpData = CacheKey::where('key', $md5Key)->first();
                $tmpData->release_time = now()->addHours($cachetime)->format('Y-m-d H:i:s');
                $tmpData->update();
            } else {
                $tmpData = new CacheKey();
                $tmpData->key = $md5Key;
                $tmpData->type = $cahcekey;
                $tmpData->set_date = $date;
                $tmpData->release_time = now()->addHours($cachetime)->format('Y-m-d H:i:s');
                $tmpData->save();
            };
            return $tmpData;
        }

        public function subPut ( $type, $condition, $data ) {
            $md5Key = md5($condition);
            Cache::store('file')->forever($md5Key, $data);

            $this->cacheKeySub()->create([ 'type' => $type, 'key' => $md5Key, 'condition' => $condition ]);
            return $this;
        }

        public function getCacheData () {
            if ( $this->key ) {
                $this->use_times += 1;
                $this->update();
                return Cache::store('file')->get($this->key);
            }
        }

        public function has ( $cahcekey ) {
            $md5Key = md5($cahcekey);
            $data = CacheKey::where('key', $md5Key)->first();

            return empty($data) ? false : true;
        }

        public function releaseCache () {
            $releaseData = CacheKey::where( 'release_time' ,'<', now())->get();
            $releaseData->each(function($v,$k){
                $keys = collect([]);
                $keys = $keys->concat($v->cacheKeySub->pluck('key')->values());
                $keys[] = $v->key;
                $v->cacheKeySub()->detach();
                $v->cacheKeySub()->delete();
                $v->delete();
                return $keys;
            })->flatten()->each(function($v){
                Cache::store('file')->forget($v);
            });
        }

        public function releaseCacheBySetDate ($setDates) {
            $releaseData = CacheKey::whereIn( 'set_date' ,$setDates)->get();
            $releaseData->each(function($v,$k){
                $keys = collect([]);
                $keys = $keys->concat($v->cacheKeySub->pluck('key')->values());
                $keys[] = $v->key;
                $v->cacheKeySub()->detach();
                $v->cacheKeySub()->delete();
                $v->delete();
                return $keys;
            })->flatten()->each(function($v){
                Cache::store('file')->forget($v);
            });
        }
    }
