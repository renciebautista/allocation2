<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddUlpOnSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->string('ulp_premium')->after('final_total_cost');
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
			$table->dropColumn(array('ulp_premium'));
		});
	}

}
