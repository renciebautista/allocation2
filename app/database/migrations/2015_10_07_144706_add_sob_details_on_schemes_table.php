<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSobDetailsOnSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->date('sob_start_date')->after('updating')->nullable();
			$table->integer('weeks')->after('sob_start_date')->nullable();
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
			$table->dropColumn(array('sob_start_date', 'weeks'));
		});
	}

}
