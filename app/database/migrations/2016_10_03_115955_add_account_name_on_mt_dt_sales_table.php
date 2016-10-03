<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAccountNameOnMtDtSalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mt_dt_sales', function(Blueprint $table)
		{
			$table->string('account_name')->after('plant_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mt_dt_sales', function(Blueprint $table)
		{
			$table->dropColumn(['account_name']);
		});
	}

}
