<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveTradeDealOnLevel5 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('level5', function(Blueprint $table)
		{
			$table->dropColumn(['trade_deal']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('level5', function(Blueprint $table)
		{
			$table->boolean('trade_deal');
		});
	}

}
