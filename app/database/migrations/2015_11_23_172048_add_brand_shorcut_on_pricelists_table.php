<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBrandShorcutOnPricelistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pricelists', function(Blueprint $table)
		{
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
		Schema::table('pricelists', function(Blueprint $table)
		{
			$table->dropColumn(array('brand_shortcut'));
		});
	}

}
