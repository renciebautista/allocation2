<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveLeadtimeOnShipTosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ship_tos', function(Blueprint $table)
		{
			$table->dropColumn(['dayofweek','default', 'npi']);
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
			$table->integer('dayofweek')->after('split')->default(0);
			$table->integer('default')->after('sun')->default(0);
			$table->integer('npi')->after('default')->default(0);
		});
	}

}
