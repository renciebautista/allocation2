<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOutletSalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('outlet_sales', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('area_code');
			$table->string('customer_code');
			$table->string('account_name');
			$table->string('outlet_code');
			$table->string('child_sku_code');
			$table->decimal('gsv',15,3);

			$table->index('area_code');
			$table->index('customer_code');
			$table->index('account_name');
			$table->index('outlet_code');
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
		Schema::drop('outlet_sales');
	}

}
