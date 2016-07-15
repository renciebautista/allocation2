<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradeDealsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeals', function(Blueprint $table)
		{

			$table->engine = "MyISAM";
			
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
			$table->decimal('alloc_in_weeks',12,2);
			$table->decimal('coverage',12,2);
			$table->boolean('non_ulp_premium');
			$table->string('non_ulp_premium_desc')->nullable();
			$table->string('non_ulp_premium_code')->nullable();
			$table->decimal('non_ulp_premium_cost',12,2)->nullable();
			$table->timestamps();

			$table->foreign('activity_id')->references('id')->on('activities');

		});


	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tradedeals');
	}

}
