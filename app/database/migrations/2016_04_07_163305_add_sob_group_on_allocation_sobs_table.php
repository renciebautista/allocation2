<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSobGroupOnAllocationSobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocation_sobs', function(Blueprint $table)
		{
			$table->integer('sob_group_id')->unsigned()->default(1)->after('receipt_date');
			$table->foreign('sob_group_id')->references('id')->on('sob_groups');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('allocation_sobs', function(Blueprint $table)
		{
			$table->dropColumn(['sob_group_id']);
		});
	}

}
