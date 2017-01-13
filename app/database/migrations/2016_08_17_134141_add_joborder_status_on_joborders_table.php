<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddJoborderStatusOnJobordersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('joborders', function(Blueprint $table)
		{
			$table->integer('joborder_status_id')->unsigned()->default(1)->nullable();
			$table->foreign('joborder_status_id')->references('id')->on('joborder_status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('joborders', function(Blueprint $table)
		{
			$table->dropColumn(['joborder_status_id']);
		});
	}

}
