<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateActivitySkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_skus', function(Blueprint $table)
		{
			$table->dropColumn(array('sap_desc',
				'pack_size',
				'barcode',
				'case_code',
				'price_case',
				'price_case_tax',
				'price',
				'srp'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_skus', function(Blueprint $table)
		{
			$table->string('sap_code');
			$table->string('sap_desc');
			$table->integer('pack_size');
			$table->string('barcode');
			$table->string('case_code');
			$table->decimal('price_case',10,2);
			$table->decimal('price_case_tax',10,2);
			$table->decimal('price',10,2);
			$table->decimal('srp',10,2);
		});
	}

}
