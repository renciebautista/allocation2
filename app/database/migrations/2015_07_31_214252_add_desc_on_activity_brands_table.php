<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnActivityBrandsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_brands', function(Blueprint $table)
		{
			$table->string('brand_desc')->after('brand_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_brands', function(Blueprint $table)
		{
			$table->dropColumn(['brand_desc']);
		});
	}

}
