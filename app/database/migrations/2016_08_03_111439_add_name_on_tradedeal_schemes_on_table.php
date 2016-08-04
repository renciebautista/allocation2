<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddNameOnTradedealSchemesOnTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_schemes', function(Blueprint $table)
		{
			$table->string('name')->after('tradedeal_id');
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
			$table->dropColumn(['name']);
		});
	}

}
