<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateDecimalsOnSalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `mt_primary_sales` CHANGE `gsv` `gsv` DECIMAL(15,3)  NOT NULL;");
		DB::statement("ALTER TABLE `dt_secondary_sales` CHANGE `gsv` `gsv` DECIMAL(15,3)  NOT NULL;");
		
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `mt_primary_sales` CHANGE `gsv` `gsv` DECIMAL(15,2)  NOT NULL;");
		DB::statement("ALTER TABLE `dt_secondary_sales` CHANGE `gsv` `gsv` DECIMAL(15,2)  NOT NULL;");
	}

}
