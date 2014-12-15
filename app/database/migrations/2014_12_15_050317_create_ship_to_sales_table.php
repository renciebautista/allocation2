<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShipToSalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ship_to_sales', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('ship_to_code');
			$table->string('child_sku_code');
			$table->decimal('gsv',15,3);

			$table->index('ship_to_code');
			$table->index('child_sku_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ship_to_sales');
	}

}
