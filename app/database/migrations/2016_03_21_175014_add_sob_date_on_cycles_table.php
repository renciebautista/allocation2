<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSobDateOnCyclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cycles', function(Blueprint $table)
		{
			$table->date('sob_deadline')->after('pdf_deadline')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cycles', function(Blueprint $table)
		{
			$table->dropColumn(['sob_deadline']);
		});
	}

}
