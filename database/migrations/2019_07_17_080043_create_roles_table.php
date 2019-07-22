<?php
	
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Database\Migrations\Migration;
	
	class CreateRolesTable extends Migration
	{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up ()
		{
			Schema::create('roles', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name');//角色名字
				$table->string('lable')->nullable();//描述;
				$table->timestamps();
				
			});
			
			// 權限表
			
			Schema::create('permissions', function (Blueprint $table) {
				
				$table->increments('id');
				$table->string('name');//角色名字
				$table->string('lable')->nullable();//描述;
				$table->timestamps();
				
			});
			
			//角色_權限表
			
			Schema::create('permission_role', function (Blueprint $table) {
				
				$table->integer('permission_id')->unsigned();
				$table->integer('role_id')->unsigned();
				
				//設置外鍵約束
				$table->foreign('permission_id')
				 ->references('id')
				 ->on('permissions')
				 ->onDelete('cascade');
				
				$table->foreign('role_id')
				 ->references('id')
				 ->on('roles')
				 ->onDelete('cascade');
				
				$table->primary(['permission_id', 'role_id']);//設置主鍵
				
			});
			
			//角色_用戶表
			
			Schema::create('role_user', function (Blueprint $table) {
				
				$table->unsignedBigInteger('user_id');
				$table->unsignedInteger('role_id');
				
				//設置外鍵約束
				$table->foreign('user_id')
				 ->references('id')
				 ->on('users')
				 ->onDelete('cascade');
				
				$table->foreign('role_id')
				 ->references('id')
				 ->on('roles')
				 ->onDelete('cascade');
				
				$table->primary(['user_id', 'role_id']);//設置主鍵
				
			});
		}
		
		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down ()
		{
			Schema::table('permission_role', function(Blueprint $table) {
				$table->dropForeign(['permission_id']);
				$table->dropForeign(['role_id']);
			});
			Schema::dropIfExists('permission_role');
			
			Schema::table('role_user', function(Blueprint $table) {
				$table->dropForeign(['user_id']);
				$table->dropForeign(['role_id']);
			});
			Schema::dropIfExists('role_user');
			
			Schema::dropIfExists('roles');
			Schema::dropIfExists('permissions');
			
			
		}
	}
