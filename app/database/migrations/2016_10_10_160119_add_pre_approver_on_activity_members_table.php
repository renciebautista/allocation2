<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPreApproverOnActivityMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_members', function(Blueprint $table)
		{
			$table->boolean('pre_approve')->default(0)->after('user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_members', function(Blueprint $table)
		{
			$table->dropColumn(['pre_approve']);
		});
	}

}
