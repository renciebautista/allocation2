<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNewOutletSalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('new_outlet_sales', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('area_code');
			$table->string('customer_code');
			$table->string('distributor_code');
			$table->string('plant_code');
			$table->string('coc_03_code');
			$table->string('coc_04_code');
			$table->string('coc_05_code');
			$table->string('account');
			$table->string('outlet');
			$table->string('child_sku_code');
			$table->decimal('gss',15,3);
			$table->decimal('gsv',15,3);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('new_outlet_sales');
	}

}
