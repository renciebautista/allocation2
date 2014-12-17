<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCoccodeOnDtSecondarySalesTabble extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dt_secondary_sales', function(Blueprint $table)
		{
			$table->string('coc_03_code')->after('child_sku_code');
			$table->index('coc_03_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('dt_secondary_sales', function(Blueprint $table)
		{
			$table->dropColumn('coc_03_code');
		});
	}

}
