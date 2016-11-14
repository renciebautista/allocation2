<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFinalPcsOnTradedealSchemeAllocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_allocations', function(Blueprint $table)
		{
			$table->decimal('manual_pcs', 12,2)->after('computed_pcs');
			$table->decimal('final_pcs', 12,2)->after('manual_pcs');
			$table->decimal('prem_cost', 12,2)->after('final_pcs');
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
			$table->dropColumn(['manual_pcs', 'final_pcs', 'prem_cost']);
		});
	}

}
