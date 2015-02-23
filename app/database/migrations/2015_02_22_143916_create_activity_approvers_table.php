<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityApproversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_approvers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
			$table->foreign('activity_id')->references('id')->on('activities');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_approvers', function(Blueprint $table)
		{
			$table->dropForeign('activity_approvers_activity_id_foreign');
			$table->dropForeign('activity_approvers_user_id_foreign');
		});
		
		Schema::drop('activity_approvers');
	}

}
