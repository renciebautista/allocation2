<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMotherChildSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mother_child_skus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('mother_sku');
			$table->string('child_sku');

			$table->index('mother_sku');
			$table->index('child_sku');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mother_child_skus');
	}

}
