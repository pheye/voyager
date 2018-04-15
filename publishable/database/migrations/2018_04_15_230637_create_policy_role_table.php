<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePolicyRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('policy_role', function(Blueprint $table)
		{
			$table->timestamps();
			$table->integer('policy_id');
			$table->integer('role_id');
			$table->string('value', 128)->nullable();
			$table->primary(['role_id','policy_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('policy_role');
	}

}
