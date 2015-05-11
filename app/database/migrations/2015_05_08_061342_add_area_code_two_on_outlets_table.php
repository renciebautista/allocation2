<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAreaCodeTwoOnOutletsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('outlets', function(Blueprint $table)
		{
			$table->string('area_code_two')->nullable()->after('area_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('outlets', function(Blueprint $table)
		{
			$table->dropColumn(array('area_code_two'));
		});
	}

}
