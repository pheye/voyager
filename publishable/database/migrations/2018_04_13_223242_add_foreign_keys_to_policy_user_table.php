<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPolicyUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('policy_user', function(Blueprint $table)
		{
			$table->foreign('policy_id')->references('id')->on('policies')->onUpdate('NO ACTION')->onDelete('CASCADE');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('policy_user', function(Blueprint $table)
		{
			$table->dropForeign('policy_user_policy_id_foreign');
			$table->dropForeign('policy_user_user_id_foreign');
		});
	}

}
