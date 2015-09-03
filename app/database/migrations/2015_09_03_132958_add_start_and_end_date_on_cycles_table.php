<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddStartAndEndDateOnCyclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cycles', function(Blueprint $table)
		{
			$table->date('start_date')->after('month_year');
			$table->date('end_date')->after('start_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cycles', function(Blueprint $table)
		{
			$table->dropColumn(array('start_date', 'end_date'));
		});
	}

}
