<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnActivityMaterialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_materials', function(Blueprint $table)
		{
			$table->string('source_desc')->after('source_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_materials', function(Blueprint $table)
		{
			$table->dropColumn(['source_desc']);
		});
	}

}
