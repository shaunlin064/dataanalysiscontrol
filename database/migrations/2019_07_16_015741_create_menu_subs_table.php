<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuSubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_subs', function (Blueprint $table) {
            $table->bigIncrements('id');
		        $table->string('name',100)->nullable();
		        $table->integer('menu_id')->nullable();
	          $table->integer('priority')->default(1);
		        $table->string('url',150)->nullable();
		        $table->string('icon',150)->nullable();
		        $table->string('title',200)->nullable();
		        $table->enum('target', ['_self', '_blank'])->default('_self');
		        $table->timestamps();
	
	          $table->index('menu_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_subs');
    }
}
