<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnActivityNobudgetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_nobudgets', function(Blueprint $table)
		{
			$table->string('budget_desc')->after('budget_type_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_nobudgets', function(Blueprint $table)
		{
			$table->dropColumn(array('budget_desc'));
		});
	}

}
