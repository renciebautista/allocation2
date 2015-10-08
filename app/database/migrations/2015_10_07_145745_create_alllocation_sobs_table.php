<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlllocationSobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('allocation_sobs', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->integer('scheme_id')->unsigned();
			$table->bigInteger('allocation_id')->unsigned();
            
            $table->integer('weekno');
            $table->integer('allocation');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('allocation_sobs');
	}

}
