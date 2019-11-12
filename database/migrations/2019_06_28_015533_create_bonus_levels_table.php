<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
	          $table->integer('bonus_id');
		        $table->integer('achieving_rate');
		        $table->double( 'bonus_rate');
	          $table->integer('bonus_direct')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus_levels');
    }
}
