<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePolicyUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('policy_user', function(Blueprint $table)
		{
			$table->integer('policy_id')->unsigned()->index('policy_user_policy_id_foreign');
			$table->integer('user_id')->unsigned()->index('policy_user_user_id_foreign');
			$table->string('value', 128)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('policy_user');
	}

}
