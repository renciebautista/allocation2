<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnActivitySkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_skus', function(Blueprint $table)
		{
			$table->string('sap_desc')->after('sap_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_skus', function(Blueprint $table)
		{
			$table->dropColumn(['sap_desc']);
		});
	}

}
