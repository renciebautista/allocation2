<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBrandOnActivityBrandsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_brands', function(Blueprint $table)
		{
			// $table->string('b_code')->after('brand_desc');
			$table->string('b_desc')->after('brand_desc');
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
			$table->dropColumn(array('b_desc'));
		});
	}

}
