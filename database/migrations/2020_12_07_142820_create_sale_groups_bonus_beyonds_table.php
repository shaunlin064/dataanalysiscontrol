<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleGroupsBonusBeyondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_groups_bonus_beyonds', function (Blueprint $table) {
	        $table->bigIncrements('id');
	        $table->integer('status'); // 狀態 0未放款 1已放款
	        $table->bigInteger('sale_groups_id');
	        $table->date('set_date');
	        $table->double('provide_money');
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
        Schema::dropIfExists('sale_groups_bonus_beyonds');
    }
}
