<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPcsDealOnTradedealSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_schemes', function(Blueprint $table)
		{
			$table->integer('pcs_deal')->nullable()->after('pre_pcs_case');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_schemes', function(Blueprint $table)
		{
			$table->dropColumn(['pcs_deal']);
		});
	}

}
