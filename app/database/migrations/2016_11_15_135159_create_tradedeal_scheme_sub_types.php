<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealSchemeSubTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_scheme_sub_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('tradedeal_scheme_id');
			$table->string('sub_type');
			$table->string('sub_type_desc');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tradedeal_scheme_sub_types');
	}

}
