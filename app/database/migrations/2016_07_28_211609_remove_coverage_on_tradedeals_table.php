<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveCoverageOnTradedealsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeals', function(Blueprint $table)
		{
			$table->dropColumn(['coverage']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeals', function(Blueprint $table)
		{
			$table->decimal('coverage', 12, 2)->after('alloc_in_weeks');
		});
	}

}
