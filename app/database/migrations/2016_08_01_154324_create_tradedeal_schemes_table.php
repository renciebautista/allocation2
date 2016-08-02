<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_schemes', function(Blueprint $table)
		{
			$table->engine = "MyISAM";

			$table->increments('id');
			$table->integer('tradedeal_id')->unsigned();
			$table->foreign('tradedeal_id')->references('id')->on('tradedeals');
			$table->integer('tradedeal_type_id')->unsigned();
			$table->foreign('tradedeal_type_id')->references('id')->on('tradedeal_types');
			$table->integer('buy');
			$table->integer('free');
			$table->decimal('coverage', 12, 2);
			$table->integer('tradedeal_uom_id')->unsigned();
			$table->foreign('tradedeal_uom_id')->references('id')->on('tradedeal_uoms');
			$table->string('pre_code');
			$table->string('pre_desc');
			$table->decimal('pre_cost',10,2);
			$table->integer('pre_pcs_case');
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
		Schema::drop('tradedeal_schemes');
	}

}
