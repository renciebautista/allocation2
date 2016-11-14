<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPremiumOnTradedealSchemeAllocationsTable extends Migration {

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
			$table->string('pre_code')->after('computed_pcs');
			$table->string('pre_desc')->after('pre_code');
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
			$table->engine = "MyISAM";
			$table->dropColumn(['pre_code', 'pre_desc']);
		});
	}

}
