<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSchemePremuimSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('scheme_premuim_skus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('scheme_id')->unsigned();
			$table->foreign('scheme_id')->references('id')->on('schemes');
			$table->string('sap_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('scheme_premuim_skus');
	}

}
