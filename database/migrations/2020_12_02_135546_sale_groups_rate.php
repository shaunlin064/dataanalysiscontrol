<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SaleGroupsRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	    Schema::create('sale_groups_rates', function (Blueprint $table) {
		    $table->id();
		    $table->unsignedInteger('sale_groups_id');
		    $table->date('set_date');
		    $table->unsignedDouble( 'rate');
	    });
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
	    Schema::dropIfExists('sale_groups_rates');
    }
}
