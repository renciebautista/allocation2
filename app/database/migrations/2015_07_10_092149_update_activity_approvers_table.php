<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateActivityApproversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_approvers', function(Blueprint $table)
		{
			$table->dropColumn(array('created_at', 'updated_at'));
			$table->integer('for_approval')->after('show');
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
			$table->timestamps();
			$table->dropColumn(array('for_approval'));
		});
	}

}
