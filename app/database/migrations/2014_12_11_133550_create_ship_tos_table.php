<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShipTosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ship_tos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('customer_code');
			$table->string('ship_to_code')->nullable();
			$table->string('ship_to_name');
			$table->integer('split')->nullable();
			$table->boolean('active')->default(0);
			$table->index(array('customer_code' ,'ship_to_code', 'ship_to_name'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ship_tos');
	}

}
