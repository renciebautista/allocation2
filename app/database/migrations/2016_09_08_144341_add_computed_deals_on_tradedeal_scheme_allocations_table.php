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
			$table->decimal('computed_deals', 12,2)->after('sold_to_gsv');
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
			$table->dropColumn(['computed_deals', 'tradedeal_scheme_id']);
		});
	}

}
