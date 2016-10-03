<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSoldToCodeOnShipTosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ship_tos', function(Blueprint $table)
		{
			$table->string('sold_to_code')->nullable()->after('customer_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ship_tos', function(Blueprint $table)
		{
			$table->dropColumn(['sold_to_code']);
		});
	}

}
