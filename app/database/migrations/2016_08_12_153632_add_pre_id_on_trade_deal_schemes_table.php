<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPreIdOnTradeDealSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_schemes', function(Blueprint $table)
		{
			$table->integer('pre_id')->nullable()->after('free');	
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_schemes', function(Blueprint $table)
		{
			$table->dropColumn(['pre_id']);
		});
	}

}
