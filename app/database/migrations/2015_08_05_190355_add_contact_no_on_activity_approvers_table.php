<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddContactNoOnActivityApproversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_approvers', function(Blueprint $table)
		{
			$table->string('contact_no')->after('approver_desc')->nullable();
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
			$table->dropColumn(array('contact_no'));
		});
	}

}
