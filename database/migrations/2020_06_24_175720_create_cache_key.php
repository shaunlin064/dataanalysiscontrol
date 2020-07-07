<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCacheKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('cache_key', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('type');
            $table->char('set_date',30);
            $table->char('key',255);
            $table->timestamp('release_time')->nullable();
            $table->bigInteger('use_times');
            $table->timestamps();

            $table->unique('key');
        });

        Schema::create('cache_key_subs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('type');
            $table->char('key',255);
            $table->json('condition');
            $table->bigInteger('use_times');
            $table->timestamps();
        });

        Schema::create('cache_key_cache_key_subs',function( Blueprint $table){
            $table->integer('cache_key_id')->unsigned();
            $table->integer('cache_key_subs_id')->unsigned();

            $table->primary(['cache_key_id','cache_key_subs_id']);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cache_key');
        Schema::dropIfExists('cache_key_subs');
        Schema::dropIfExists('cache_key_cache_key_subs');
    }
}
