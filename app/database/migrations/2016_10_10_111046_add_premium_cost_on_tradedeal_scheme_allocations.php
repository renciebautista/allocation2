<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPremiumCostOnTradedealSchemeAllocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_allocations', function(Blueprint $table)
		{
			$table->decimal('computed_cost', 12, 2)->after('computed_pcs');
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
			$table->dropColumn(['computed_cost']);
		});
	}

}
