<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePricelistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pricelists', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('sap_code');
			$table->string('sap_desc');
			$table->integer('pack_size');
			$table->string('barcode');
			$table->string('case_code');
			$table->decimal('price_case', 10, 2);
			$table->decimal('price_case_tax', 10, 2);
			$table->decimal('price', 10, 2);
			$table->decimal('srp', 10, 2);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pricelists');
	}

}
