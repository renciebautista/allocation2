<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateAtivitiesDefaultValues extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		DB::statement("ALTER TABLE `activities` CHANGE `downloaded` `downloaded` TINYINT(1)  NOT NULL  DEFAULT '0';");
		DB::statement("ALTER TABLE `activities` CHANGE `word` `word` TINYINT(1)  NOT NULL  DEFAULT '0';");
		DB::statement("ALTER TABLE `activity_approvers` CHANGE `status_id` `status_id` INT(10)  UNSIGNED  NOT NULL  DEFAULT '0';");
		DB::statement("ALTER TABLE `activity_approvers` CHANGE `for_approval` `for_approval` INT(11)  NOT NULL  DEFAULT '0';");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `activities` CHANGE `downloaded` `downloaded` TINYINT(1)  NOT NULL;");
		DB::statement("ALTER TABLE `activities` CHANGE `word` `word` TINYINT(1)  NOT NULL;");
		DB::statement("ALTER TABLE `activity_approvers` CHANGE `status_id` `status_id` INT(10)  UNSIGNED  NOT NULL;");
		DB::statement("ALTER TABLE `activity_approvers` CHANGE `for_approval` `for_approval` INT(11)  NOT NULL;");
	}

}
