<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_provides', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('financial_lists_id')->unique();
	          $table->bigInteger('bonus_id');
	          $table->Integer('provide_money');
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
        Schema::dropIfExists('financial_provides');
    }
}
