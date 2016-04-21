<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLoadingDaysOnShipTosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ship_tos', function(Blueprint $table)
		{
			$table->boolean('mon')->after('leadtime');
			$table->boolean('tue')->after('mon');
			$table->boolean('wed')->after('tue');
			$table->boolean('thu')->after('wed');
			$table->boolean('fri')->after('thu');
			$table->boolean('sat')->after('fri');
			$table->boolean('sun')->after('sat');
			$table->integer('default')->after('sun')->default(0);
			$table->integer('npi')->after('default')->default(0);
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
			$table->dropColumn(['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'default', 'npi']);
		});
	}

}
