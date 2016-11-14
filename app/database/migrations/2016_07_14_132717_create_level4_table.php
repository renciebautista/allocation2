<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLevel4Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('level4', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('coc_03_code');
			$table->string('l4_code');
			$table->string('l4_desc');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('level4');
	}

}
