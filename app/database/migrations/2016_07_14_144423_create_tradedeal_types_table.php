<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Schema::create('tradedeal_types', function(Blueprint $table)
		// {
		// 	$table->increments('id');
		// 	$table->string('tradedeal_type');
		// 	$table->boolean('active');
		// });

		// DB::statement("INSERT INTO tradedeal_types (id, tradedeal_type, active) VALUES
		// 	(1, 'INDIVIDUAL', '1'),
		// 	(2, 'COLLECTIVE BASKET', '0'),
		// 	(3, 'COLLECTIVE', '1');");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tradedeal_types');
	}

}
