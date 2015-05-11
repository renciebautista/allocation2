<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMultiOnAllocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `allocations` CHANGE `multi` `multi` DECIMAL(12,5) NULL DEFAULT NULL;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `allocations` CHANGE `multi` `multi` DECIMAL(12,2) NULL DEFAULT NULL;');
	}

}
