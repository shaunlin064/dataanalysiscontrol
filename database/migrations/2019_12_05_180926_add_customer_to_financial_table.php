<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerToFinancialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_lists', function (Blueprint $table) {
            //
            $table->bigInteger('companies_id')->nullable();
            $table->char('companies_name',100)->nullable();
            $table->bigInteger('agency_id');
            $table->char('agency_name',100)->nullable();
            $table->bigInteger('client_id');
            $table->char('client_name',100)->nullable();
            $table->char('sales_channel',10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_lists', function (Blueprint $table) {
            $table->dropColumn('companies_id');
            $table->dropColumn('agency_id');
            $table->dropColumn('client_id');
            $table->dropColumn('companies_name');
            $table->dropColumn('agency_name');
            $table->dropColumn('client_name');
            $table->dropColumn('sales_channel');
        });
    }
}
