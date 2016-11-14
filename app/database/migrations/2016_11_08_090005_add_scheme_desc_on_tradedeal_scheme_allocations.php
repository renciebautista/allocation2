<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSchemeDescOnTradedealSchemeAllocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_allocations', function(Blueprint $table)
		{
			$table->string('scheme_desc')->after('scheme_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_scheme_allocations', function(Blueprint $table)
		{
			$table->dropColumn(['scheme_desc']);
		});
	}

}
