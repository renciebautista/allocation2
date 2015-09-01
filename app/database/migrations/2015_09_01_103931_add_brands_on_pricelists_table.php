<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBrandsOnPricelistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pricelists', function(Blueprint $table)
		{
			$table->string('division_code')->after('sap_desc');
			$table->string('division_desc')->after('division_code');
			$table->string('category_code')->after('division_desc');
			$table->string('category_desc')->after('category_code');
			$table->string('brand_code')->after('category_desc');
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
		Schema::table('pricelists', function(Blueprint $table)
		{
			$table->dropColumn(array('division_code','division_desc','category_code','category_desc',
				'brand_code','brand_desc'));
		});
	}

}
