<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateCyclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cycles', function(Blueprint $table)
		{
			$table->dropColumn(array('vetting_deadline', 'replyback_deadline', 'emergency_deadline', 'emergency_release_date'));
			$table->date('approval_deadline')->nullable();
			$table->date('pdf_deadline')->nullable();
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
			$table->dropColumn(array('approval_deadline', 'pdf_deadline'));
			$table->date('vetting_deadline')->nullable();
			$table->date('replyback_deadline')->nullable();
			$table->date('emergency_deadline')->nullable();
			$table->date('emergency_release_date')->nullable();
		});
	}

}
