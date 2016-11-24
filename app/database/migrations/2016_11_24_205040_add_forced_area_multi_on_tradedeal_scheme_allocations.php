<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForcedAreaMultiOnTradedealSchemeAllocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_allocations', function(Blueprint $table)
		{
			$table->decimal('forced_sold_to_gsv', 15, 2)->after('sold_to_gsv');
			$table->decimal('forced_weekly_run_rates', 15, 2)->after('weekly_run_rates');
			$table->decimal('forced_computed_pcs', 12, 2)->after('computed_pcs');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_scheme_allocations', function(Blueprint $table)
		{
			$table->dropColumn(['forced_sold_to_gsv', 'forced_weekly_run_rates', 'forced_computed_pcs']);
		});
	}

}
