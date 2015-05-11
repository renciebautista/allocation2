<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddMonthYearOnCycles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cycles', function(Blueprint $table)
		{
			$table->string('month_year')->after('year');
			$table->dropForeign('cycles_month_id_foreign');
			$table->dropColumn(array('month_id'));
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
			$table->dropColumn(array('month_year'));
			$table->integer('month_id')->unsigned()->nullable()->after('cycle_name');
			$table->foreign('month_id')->references('id')->on('months');
		});
	}

}
