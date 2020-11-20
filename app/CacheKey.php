<?php


    namespace App;

    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;

    class Cachekey extends model {
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
            return Cache::remember($key, $time, $callback);
        }

        public function put ( $cahcekey, $date, $data, $cachetime ) {

            $md5Key = md5($cahcekey . $date);

            Cache::forever($md5Key, $data);

            if ( !empty(Cachekey::where('key', $md5Key)->first()) ) {
                $tmpData = Cachekey::where('key', $md5Key)->first();
                $tmpData->release_time = now()->addHours($cachetime)->format('Y-m-d H:i:s');
                $tmpData->update();
            } else {
                $tmpData = new Cachekey();
                $tmpData->key = $md5Key;
                $tmpData->type = $cahcekey;
                $tmpData->set_date = $date;
                $tmpData->release_time = now()->addHours($cachetime)->format('Y-m-d H:i:s');
                $tmpData->save();
            };
            return $tmpData;
        }

        public function subPut ( $type, $condition, $data ) {
	        $this->cacheKeySub()->detach();
            $md5Key = md5($condition);
            Cache::forever($md5Key, $data);
            $this->cacheKeySub()->create([ 'type' => $type, 'key' => $md5Key, 'condition' => $condition ]);
            return $this;
        }

        public function getCacheData () {
            if ( $this->key ) {
                $this->use_times += 1;
                $this->update();
                return Cache::get($this->key);
            }
        }

        public function has ( $cahcekey ) {
            $md5Key = md5($cahcekey);
            $data = Cachekey::where('key', $md5Key)->first();

            return empty($data) ? false : true;
        }

        public function releaseCache () {
            $releaseData = Cachekey::where('release_time' ,'<', now())->get();

            $missDataCacheKeySubIds = DB::table('cache_key_subs')->selectRaw('id')->leftJoin('cache_key_cache_key_subs','id','cache_key_cache_key_subs.cache_key_subs_id')->where('cache_key_subs_id',null)->pluck('id');

            $keys = collect([]);
            $releaseData->each(function($v,$k) use(&$keys){
                $keys[] = $keys->concat($v->cacheKeySub->pluck('key')->values());
                $keys[] = $v->key;
                $v->cacheKeySub()->detach();
                $v->delete();
            });
            $key = $keys->flatten();

            CacheKeySub::whereIn('key',$key)->orWhereIn('id',$missDataCacheKeySubIds)->delete();
            $keys->each(function($v){
                Cache::forget($v);
            });
        }

        /**
         * @param Collection[CacheKey] $data
         */
        static function releaseCacheByDatas (Collection $data):void {
        	
            $missDataCacheKeySubIds = DB::table('cache_key_subs')->selectRaw('id')->leftJoin('cache_key_cache_key_subs','id','cache_key_cache_key_subs.cache_key_subs_id')->where('cache_key_subs_id',null)->pluck('id');
	
	        $keys = $data->map(function($v,$k) {
                $v->cacheKeySub()->detach();
                $v->delete();
		        return $v->key;
            })->each(function($v){
		        Cache::forget($v);
	        });;
            CacheKeySub::whereIn('key',$keys)->orWhereIn('id',$missDataCacheKeySubIds)->delete();
            
        }
    }
