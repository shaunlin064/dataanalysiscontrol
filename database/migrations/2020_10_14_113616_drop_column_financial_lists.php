<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnFinancialLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	    Schema::table('financial_lists', function (Blueprint $table) {
		    $table->dropColumn(['agency_name','client_name','companies_name']);
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
	    Schema::table('financial_lists', function (Blueprint $table) {
		    $table->char('companies_name',100)->nullable();
		    $table->char('agency_name',100)->nullable();
		    $table->char('client_name',100)->nullable();
	    });
    }
}
