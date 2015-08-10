<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescSchemeHostSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('scheme_host_skus', function(Blueprint $table)
		{
			$table->string('sap_desc')->after('sap_code');
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
		Schema::table('scheme_host_skus', function(Blueprint $table)
		{
			$table->dropColumn(array('sap_desc', 'pack_size','barcode','case_code','price_case','price_case_tax','price','srp'));
		});
	}

}
