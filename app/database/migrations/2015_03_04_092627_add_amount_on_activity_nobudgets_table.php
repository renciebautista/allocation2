<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAmountOnActivityNobudgetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_nobudgets', function(Blueprint $table)
		{
			$table->decimal('amount',12,2);
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
			$table->dropColumn(array('amount'));
		});
	}

}
