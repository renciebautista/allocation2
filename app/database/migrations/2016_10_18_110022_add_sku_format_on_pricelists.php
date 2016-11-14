<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSkuFormatOnPricelists extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pricelists', function(Blueprint $table)
		{
			$table->string('sku_format')->after('srp')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pricelists', function(Blueprint $table)
		{
			$table->dropColumn(['sku_format']);
		});
	}

}
