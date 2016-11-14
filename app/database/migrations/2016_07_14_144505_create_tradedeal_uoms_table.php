<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealUomsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_uoms', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('tradedeal_uom');
		});

		DB::statement("INSERT INTO tradedeal_uoms (id, tradedeal_uom) VALUES
			(1, 'PIECES'),
			(2, 'DOZENS'),
			(3, 'CASES');");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tradedeal_uoms');
	}

}
