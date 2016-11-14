<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLevelsOnMtDtSalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mt_dt_sales', function(Blueprint $table)
		{
			$table->string('coc_04_code')->after('coc_03_code');
			$table->string('coc_05_code')->after('coc_04_code');
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
			$table->dropColumn(['coc_04_code', 'coc_05_code']);
		});
	}

}
