<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTradedealOnActivityTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_types', function(Blueprint $table)
		{
			$table->boolean('with_tradedeal')->default(false)->after('with_sob');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_types', function(Blueprint $table)
		{
			$table->dropColumn(['with_tradedeal']);
		});
	}

}
