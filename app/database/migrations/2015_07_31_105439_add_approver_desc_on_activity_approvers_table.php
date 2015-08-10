<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddApproverDescOnActivityApproversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_approvers', function(Blueprint $table)
		{
			$table->string('approver_desc')->after('user_id');
			$table->integer('group_id')->after('approver_desc');
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
			$table->dropColumn(['approver_desc', 'group_id']);
		});
	}

}
