<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnForceAllocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('force_allocations', function(Blueprint $table)
		{
			$table->string('group_code')->after('activity_id');
			$table->string('group_desc')->after('group_code');
			$table->string('area_desc')->after('area_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('force_allocations', function(Blueprint $table)
		{
			$table->dropColumn(['area_desc']);
		});
	}

}
