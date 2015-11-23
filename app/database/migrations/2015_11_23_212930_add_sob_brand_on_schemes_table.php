<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSobBrandOnSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->string('brand_code')->after('weeks')->nullable();
			$table->string('brand_desc')->after('brand_code')->nullable();
			$table->string('brand_shortcut')->after('brand_desc')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->dropColumn(array('brand_code', 'brand_desc', 'brand_shortcut'));
		});
	}

}
