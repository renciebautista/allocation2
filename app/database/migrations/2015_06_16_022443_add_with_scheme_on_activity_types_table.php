<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddWithSchemeOnActivityTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_types', function(Blueprint $table)
		{
			$table->boolean('with_scheme')->after('uom')->default(true);
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
			$table->dropColumn(array('with_scheme'));
		});
	}

}
