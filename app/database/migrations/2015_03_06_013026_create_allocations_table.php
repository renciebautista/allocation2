<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAllocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('allocations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('scheme_id')->unsigned();
			$table->foreign('scheme_id')->references('id')->on('schemes');
			$table->integer('customer_id')->unsigned()->nullable();
			$table->integer('shipto_id')->unsigned()->nullable();
			$table->string('group')->nullable();
			$table->string('area')->nullable();
			$table->string('sold_to')->nullable();
			$table->string('ship_to')->nullable();
			$table->string('channel')->nullable();
			$table->string('outlet')->nullable();
			$table->decimal('sold_to_gsv',12,2)->nullable();
			$table->decimal('sold_to_gsv_p',12,2)->nullable();
			$table->integer('sold_to_alloc')->nullable();
			$table->decimal('ship_to_gsv',12,2)->nullable();
			$table->decimal('ship_to_gsv_p',12,2)->nullable();
			$table->integer('ship_to_alloc')->nullable();
			$table->decimal('outlet_to_gsv',12,2)->nullable();
			$table->decimal('outlet_to_gsv_p',12,2)->nullable();
			$table->integer('outlet_to_alloc')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('allocations');
	}

}
