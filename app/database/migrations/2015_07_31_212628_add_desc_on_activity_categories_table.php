<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnActivityCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_categories', function(Blueprint $table)
		{
			$table->string('category_desc')->after('category_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_categories', function(Blueprint $table)
		{
			$table->dropColumn(['category_desc']);
		});
	}

}
