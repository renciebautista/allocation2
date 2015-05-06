<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddShowOnActivityApproversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_approvers', function(Blueprint $table)
		{
			$table->boolean('show')->default(false)->after('status_id');
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
			$table->dropColumn(array('show'));
		});
	}

}
