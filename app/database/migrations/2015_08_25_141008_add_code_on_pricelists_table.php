<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCodeOnPricelistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pricelists', function(Blueprint $table)
		{
			$table->string('division_code')->after('id');
			$table->string('category_code')->after('division_code');
			$table->string('cpg_code')->after('category_code');
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
			$table->dropColumn(array('division_code', 'category_code', 'cpg_code'));
		});
	}

}
