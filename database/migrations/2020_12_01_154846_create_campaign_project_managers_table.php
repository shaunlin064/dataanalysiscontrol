<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignProjectManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_project_managers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('erp_user_id');
            $table->tinyInteger('type')->comment('pm類型 1 媒體 2口碑 3編輯');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_project_managers');
    }
}
