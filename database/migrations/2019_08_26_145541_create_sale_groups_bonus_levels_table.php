<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleGroupsBonusLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_groups_bonus_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
	          $table->bigInteger('sale_groups_id');
	          $table->date('set_date');
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
        Schema::dropIfExists('sale_groups_bonus_levels');
    }
}
