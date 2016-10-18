<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSkuFormatOnTradedealPartSkus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->string('host_sku_format')->after('brand_shortcut');
			$table->string('pre_sku_format')->after('pre_brand_shortcut');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->dropColumn(['host_sku_format', 'pre_sku_format']);
		});
	}

}
