<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddComputedDealsOnTradedealSchemeAllocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_allocations', function(Blueprint $table)
		{
			$table->engine = "MyISAM";
			$table->integer('tradedeal_scheme_id')->unsigned()->after('id');
			$table->foreign('tradedeal_scheme_id')->references('id')->on('tradedeal_schemes');
			$table->decimal('weekly_run_rates', 12,2)->after('sold_to_gsv');
			$table->decimal('pur_req', 12,2)->after('weekly_run_rates');
			$table->decimal('computed_pcs', 12,2)->after('pur_req');
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
			$table->dropForeign('tradedeal_scheme_id_foreign');
			$table->dropColumn(['computed_pcs', 'tradedeal_scheme_id', 'weekly_run_rates', 'pur_req']);
		});
	}

}
