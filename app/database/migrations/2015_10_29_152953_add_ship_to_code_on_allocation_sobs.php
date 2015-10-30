<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddShipToCodeOnAllocationSobs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocation_sobs', function(Blueprint $table)
		{
			$table->string('ship_to_code')->after('allocation_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('allocation_sobs', function(Blueprint $table)
		{
			$table->dropColumn(array('ship_to_code'));
		});
	}

}
