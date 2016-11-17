<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIndexOnMtDtSales extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Schema::table('sub_channels', function(Blueprint $table)
		// {
		// 	$table->index('coc_03_code');
		// 	$table->index('l4_code');
		// 	$table->index('l5_code');
		// });

		// Schema::table('mt_dt_sales', function(Blueprint $table)
		// {
		// 	$table->index('area_code');
		// 	$table->index('customer_code');
		// 	$table->index('distributor_code');
		// 	$table->index('plant_code');
		// 	$table->index('account_name');
		// 	$table->index('coc_03_code');
		// 	$table->index('coc_04_code');
		// 	$table->index('coc_05_code');
		// });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Schema::table('sub_channels', function(Blueprint $table)
		// {
		// 	$table->dropIndex('sub_channels_coc_03_code_index');
		// 	$table->dropIndex('sub_channels_l4_code_index');
		// 	$table->dropIndex('sub_channels_l5_code_index');
		// });

		// Schema::table('mt_dt_sales', function(Blueprint $table)
		// {
		// 	$table->dropIndex('mt_dt_sales_area_code_index');
		// 	$table->dropIndex('mt_dt_sales_customer_code_index');
		// 	$table->dropIndex('mt_dt_sales_distributor_code_index');
		// 	$table->dropIndex('mt_dt_sales_plant_code_index');
		// 	$table->dropIndex('mt_dt_sales_account_name_index');
		// 	$table->dropIndex('mt_dt_sales_coc_03_code_index');
		// 	$table->dropIndex('mt_dt_sales_coc_04_code_index');
		// 	$table->dropIndex('mt_dt_sales_coc_05_code_index');
		// });

		
	}

}
