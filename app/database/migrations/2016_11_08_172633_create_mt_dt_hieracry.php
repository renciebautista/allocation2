<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMtDtHieracry extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mt_dt_hieracry', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('area_code');
			$table->string('customer_code');
			$table->string('distributor_code');
			$table->string('plant_code');
			$table->string('account_name');
			$table->string('coc_03_code');
			$table->string('coc_04_code');
			$table->string('coc_05_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mt_dt_hieracry');
	}

}
