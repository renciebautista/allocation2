<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForceUploadOnTradeDealsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeals', function(Blueprint $table)
		{
			$table->boolean('forced_upload')->after('non_ulp_pcs_case');
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
			$table->dropColumn(['forced_upload']);
		});
	}

}
