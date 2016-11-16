<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealSubSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_sub_schemes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('tradedeal_id');
			$table->integer('tradedeal_scheme_id');
			$table->integer('tradedeal_scheme_sku_id');
			$table->string('scheme_code');
			$table->string('host_desc');
			$table->string('premium');
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
		Schema::drop('tradedeal_sub_schemes');
	}

}
