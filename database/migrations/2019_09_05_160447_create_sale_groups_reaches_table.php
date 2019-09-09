<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleGroupsReachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_groups_reaches', function (Blueprint $table) {
            $table->bigIncrements('id');
	          $table->integer('status'); // 狀態 0未放款 1已放款
		        $table->bigInteger('sale_groups_id');
		        $table->bigInteger('sale_groups_bonus_levels_id');
		        $table->bigInteger('sale_groups_users_id');
	          $table->date('set_date');
            $table->double( 'groups_profit');
	          $table->double( 'provide_money');
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
        Schema::dropIfExists('sale_groups_reaches');
    }
}
