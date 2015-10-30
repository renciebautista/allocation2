<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLoadingDateOnAllocationSobs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocation_sobs', function(Blueprint $table)
		{
			$table->date("loading_date")->after('year')->nullable();
			$table->date("receipt_date")->after('loading_date')->nullable();			
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
			$table->dropColumn(array('loading_date', 'receipt_date'));
		});
	}

}
