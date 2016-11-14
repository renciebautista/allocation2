<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPcsCaseOnTradedeals extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeals', function(Blueprint $table)
		{
			$table->integer('non_ulp_pcs_case')->nullable()->after('non_ulp_premium_cost');
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
			$table->dropColumn(['non_ulp_pcs_case']);
		});
	}

}
