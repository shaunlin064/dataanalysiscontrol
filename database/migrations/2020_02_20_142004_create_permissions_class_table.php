<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions_class', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('region')->default('user');
            $table->timestamps();
        });
        
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedInteger('permissions_class_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions_class');
        
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('permissions_class_id');
        });
    }
}
