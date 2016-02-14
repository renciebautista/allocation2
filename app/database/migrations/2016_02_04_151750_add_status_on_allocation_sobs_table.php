<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddStatusOnAllocationSobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocation_sobs', function(Blueprint $table)
		{
			$table->integer('booking_status_id')->unsigned()->default(1)->after('allocation');
			$table->foreign('booking_status_id')->references('id')->on('booking_status');
			$table->string('po_no')->after('allocation_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('allocation_sobs', function(Blueprint $table)
		{
			$table->dropColumn(array('booking_status_id'));

		});
	}

}
