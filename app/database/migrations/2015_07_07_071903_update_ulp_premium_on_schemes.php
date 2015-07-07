<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateUlpPremiumOnSchemes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `schemes` CHANGE `ulp_premium` `ulp_premium` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `schemes` CHANGE `ulp_premium` `ulp_premium` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
	}

}
