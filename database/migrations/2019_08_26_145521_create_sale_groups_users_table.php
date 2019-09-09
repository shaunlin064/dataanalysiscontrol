<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleGroupsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_groups_users', function (Blueprint $table) {
            $table->bigIncrements('id');
	          $table->bigInteger('sale_groups_id');
	          $table->bigInteger('erp_user_id');
	          $table->bigInteger('is_convener');
	          $table->date('set_date');
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
        Schema::dropIfExists('sale_groups_users');
    }
}
