<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddActivityGroupingIdOnActivityTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_types', function(Blueprint $table)
		{
			// $table->boolean('pe')->after('with_scheme');
			// $table->boolean('tts')->after('pe');
			$table->integer('activity_grouping_id')->nullable()->after('with_scheme');
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
			$table->dropColumn(array('activity_grouping_id'));
		});
	}

}
