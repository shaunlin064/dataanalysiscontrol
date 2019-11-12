<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancialListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('status'); // 狀態 0未收款 1已收款 2 已收款且放款
            $table->bigInteger('cp_detail_id'); //erp cp_detail_id
            $table->integer('erp_user_id'); // epr user id
	          $table->bigInteger('campaign_id'); // cam id
            $table->string('campaign_name'); //案件名稱
            $table->string('media_channel_name'); // 該條cue 媒體
	          $table->string('sell_type_name'); // 賣法名稱
	          $table->string('organization'); // 該案件執行公司縮寫 js hk ff
	          $table->date('set_date'); // year_month 該條cue 年月份 日 一律 01號
	          $table->string('currency'); // 幣別 原幣別
	          $table->double('income'); // 收入 已轉為台幣
	          $table->double('cost'); // 成本 已轉為台幣
	          $table->double('profit'); // 毛利 已轉為台幣
	          $table->double('profit_percentage'); //毛利率
	          
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
        Schema::dropIfExists('financial_lists');
    }
}
