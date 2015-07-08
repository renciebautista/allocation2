<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateMultiOnForceAllocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `force_allocations` CHANGE `multi` `multi` DECIMAL(15,2) NULL DEFAULT '0';");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `force_allocations` CHANGE `multi` `multi` INT(11) NULL DEFAULT '0.00';");
	}

}
