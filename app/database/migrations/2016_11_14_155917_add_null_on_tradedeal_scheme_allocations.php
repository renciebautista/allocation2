<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddNullOnTradedealSchemeAllocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE tradedeal_scheme_allocations MODIFY ship_to_code varchar(255) NULL;');
		DB::statement('ALTER TABLE tradedeal_scheme_allocations MODIFY plant_code varchar(255) NULL;');
		DB::statement('ALTER TABLE tradedeal_scheme_allocations MODIFY ship_to_name varchar(255) NULL;');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE tradedeal_scheme_allocations MODIFY ship_to_code varchar(255) NOT NULL;');
		DB::statement('ALTER TABLE tradedeal_scheme_allocations MODIFY plant_code varchar(255) NOT NULL;');
		DB::statement('ALTER TABLE tradedeal_scheme_allocations MODIFY ship_to_name varchar(255) NOT NULL;');
	}

}
