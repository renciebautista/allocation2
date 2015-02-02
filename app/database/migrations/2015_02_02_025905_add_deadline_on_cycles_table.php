<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDeadlineOnCyclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cycles', function(Blueprint $table)
		{
			$table->integer('month_id')->unsigned();
            $table->foreign('month_id')->references('id')->on('months');
			$table->date('vetting_deadline')->nullable();
			$table->date('replyback_deadline')->nullable();
			$table->date('submission_deadline')->nullable();
			$table->date('release_date')->nullable();
			$table->date('emergency_deadline')->nullable();
			$table->date('emergency_release_date')->nullable();
			$table->date('implemintation_date')->nullable();
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
			$table->dropForeign('cycles_month_id_foreign');
			$table->dropColumn(array('month_id' ,'vetting_deadline', 'replyback_deadline', 'submission_deadline',
				'release_date', 'emergency_deadline', 'emergency_release_date', 'implemintation_date'));
		});
	}

}
