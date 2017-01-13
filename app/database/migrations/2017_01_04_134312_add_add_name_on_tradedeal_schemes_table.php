<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAddNameOnTradedealSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_schemes', function(Blueprint $table)
		{
			$table->string('additional_name')->nullable()->after('name');
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
			$table->dropColumn(['additional_name']);
		});
	}

}
