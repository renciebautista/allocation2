<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivitySkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_skus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('activities');
			$table->string('sap_code');
			$table->string('sap_desc')->nullable();
			$table->integer('pack_size')->nullable();
			$table->string('barcode')->nullable();
			$table->string('case_code')->nullable();
			$table->decimal('price_case', 10, 2)->nullable();
			$table->decimal('price_case_tax', 10, 2)->nullable();
			$table->decimal('price', 10, 2)->nullable();
			$table->decimal('srp', 10, 2)->nullable();
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
			$table->dropForeign('activity_skus_activity_id_foreign');
		});
		Schema::drop('activity_skus');
	}

}
