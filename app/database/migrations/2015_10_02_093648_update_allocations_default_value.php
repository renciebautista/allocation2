<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateAllocationsDefaultValue extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// DB::statement("");
		DB::statement("ALTER TABLE `allocations` CHANGE `account_group_name` `account_group_name` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL;");
		DB::statement("ALTER TABLE `allocations` CHANGE `show` `show` TINYINT(1)  NOT NULL  DEFAULT '0';");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `allocations` CHANGE `account_group_name` `account_group_name` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci NULL;");
		DB::statement("ALTER TABLE `allocations` CHANGE `show` `show` TINYINT(1)  NOT NULL ");
	}

}
