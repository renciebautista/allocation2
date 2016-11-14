<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTradeDealOnCustomers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customers', function(Blueprint $table)
		{
			$table->boolean('trade_deal')->default(0)->after('from_dt');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customers', function(Blueprint $table)
		{
			$table->dropColumn(['trade_deal']);
		});
	}

}
