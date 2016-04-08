<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSobGroupIdOnSobFiltersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sob_filters', function(Blueprint $table)
		{
			$table->integer('sob_group_id')->unsigned()->default(1)->after('customer_code');
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
		Schema::table('sob_filters', function(Blueprint $table)
		{
			$table->dropColumn(['sob_group_id']);
		});
	}

}
