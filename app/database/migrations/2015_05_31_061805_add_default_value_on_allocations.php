<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDefaultValueOnAllocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `allocations` CHANGE `sold_to_gsv` `sold_to_gsv` DECIMAL(12,2) NULL DEFAULT '0';");
		DB::statement("ALTER TABLE `allocations` CHANGE `sold_to_gsv_p` `sold_to_gsv_p` DECIMAL(12,2) NULL DEFAULT '0'");
		DB::statement("ALTER TABLE `allocations` CHANGE `sold_to_alloc` `sold_to_alloc` INT(11) NULL DEFAULT '0'");
		DB::statement("ALTER TABLE `allocations` CHANGE `ship_to_gsv` `ship_to_gsv` DECIMAL(12,2) NULL DEFAULT '0'");
		DB::statement("ALTER TABLE `allocations` CHANGE `ship_to_gsv_p` `ship_to_gsv_p` DECIMAL(12,2) NULL DEFAULT '0'");

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `allocations` CHANGE `sold_to_gsv` `sold_to_gsv` DECIMAL(12,2) NULL DEFAULT NULL;");
		DB::statement("ALTER TABLE `allocations` CHANGE `sold_to_gsv_p` `sold_to_gsv_p` DECIMAL(12,2) NULL DEFAULT NULL");
		DB::statement("ALTER TABLE `allocations` CHANGE `sold_to_alloc` `sold_to_alloc` INT(11) NULL DEFAULT NULL;");
		DB::statement("ALTER TABLE `allocations` CHANGE `ship_to_gsv` `ship_to_gsv` DECIMAL(12,2) NULL DEFAULT NULL");
		DB::statement("ALTER TABLE `allocations` CHANGE `sold_to_gsv_p` `sold_to_gsv_p` DECIMAL(12,2) NULL DEFAULT NULL;");
	}

}
