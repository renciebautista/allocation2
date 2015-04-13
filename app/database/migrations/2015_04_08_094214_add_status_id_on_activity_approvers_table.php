<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddStatusIdOnActivityApproversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_approvers', function(Blueprint $table)
		{
			$table->integer('status_id')->unsigned();
            $table->timestamps();
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
			$table->dropColumn(array('status_id','created_at', 'updated_at'));
		});
	}

}
